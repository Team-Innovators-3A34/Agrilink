from flask import Flask, request, jsonify
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.preprocessing import StandardScaler
from sklearn.neighbors import NearestNeighbors
import requests

app = Flask(__name__)

# Cache to store matches for users
cache = {}

@app.route('/match', methods=['POST'])
def match():
    print("Route /match appelée !")  # Ajoute ce log
    data = request.get_json()
    print("Données reçues :", data)  # Ajoute ce log
    
    if not data:
        return jsonify({"error": "Données non reçues"}), 400
    data = request.get_json()
    
    user_id = data.get("user_id")
    users = data.get("users")

    if not user_id or not users:
        return jsonify({"error": "Missing data"}), 400

    df = pd.DataFrame(users)

    if user_id not in df['id'].values:
        return jsonify({"error": "User not found"}), 404

    df.fillna({"description": "", "latitude": 0, "longitude": 0, "score": 0}, inplace=True)

    interest_vectors = TfidfVectorizer().fit_transform(df['description']).toarray()
    location_vectors = StandardScaler().fit_transform(df[['latitude', 'longitude', 'score']])

    X = pd.concat([pd.DataFrame(interest_vectors), pd.DataFrame(location_vectors, index=df.index)], axis=1)

    knn = NearestNeighbors(n_neighbors=min(4, len(df)), metric='euclidean').fit(X) 
    user_index = df.index[df['id'] == user_id][0]

    distances, indices = knn.kneighbors([X.iloc[user_index]])
    matches = [int(df.iloc[i]['id']) for i in indices[0] if df.iloc[i]['id'] != user_id]

    return jsonify({"matches": matches})

if __name__ == '__main__':
    print("Routes disponibles :", app.url_map)
    app.run(host='0.0.0.0', port=5000) 
