import sys
import requests
import pandas as pd
import json
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.preprocessing import StandardScaler
from sklearn.neighbors import NearestNeighbors

# 🔹 Vérifier si l'ID utilisateur est fourni
if len(sys.argv) < 2:
    print(json.dumps({"error": "User ID is required"}))
    sys.exit(1)

user_id = int(sys.argv[1])

# 🔹 Récupérer les utilisateurs depuis l'API Symfony
url = "http://127.0.0.1:8000/api/users"  # 🔴 Mets l'URL correcte si nécessaire

try:
    response = requests.get(url)
    response.raise_for_status()
    users = response.json()
except requests.exceptions.RequestException as e:
    print(json.dumps({"error": f"Failed to fetch users: {str(e)}"}))
    sys.exit(1)

# 🔹 Vérifier si des utilisateurs ont été récupérés
if not users or not isinstance(users, list):
    print(json.dumps({"error": "No users found or invalid response"}))
    sys.exit(1)

# 🔹 Convertir les données en DataFrame
df = pd.DataFrame(users)

# 🔹 Vérifier la présence de l'utilisateur dans la base
if 'id' not in df.columns or user_id not in df['id'].values:
    print(json.dumps({"error": "User not found"}))
    sys.exit(1)

# 🔹 Remplir les valeurs manquantes pour éviter les erreurs
df['description'] = df['description'].fillna("")
df[['latitude', 'longitude', 'score']] = df[['latitude', 'longitude', 'score']].fillna(0)

# 🔹 Convertir les descriptions en vecteurs numériques (TF-IDF)
vectorizer = TfidfVectorizer()
interest_vectors = vectorizer.fit_transform(df['description']).toarray()

# 🔹 Normaliser la localisation et le score
scaler = StandardScaler()
location_vectors = scaler.fit_transform(df[['latitude', 'longitude', 'score']])

# 🔹 Fusionner les caractéristiques en un seul tableau
X = pd.concat([pd.DataFrame(interest_vectors), pd.DataFrame(location_vectors)], axis=1)

# 🔹 Entraîner le modèle KNN
knn = NearestNeighbors(n_neighbors=min(4, len(df)), metric='euclidean')  # Ajustement dynamique
knn.fit(X)

# 🔹 Trouver l'index de l'utilisateur
user_index = df[df['id'] == user_id].index[0]

# 🔹 Trouver les utilisateurs les plus proches
distances, indices = knn.kneighbors([X.iloc[user_index]], n_neighbors=min(4, len(df)))

# 🔹 Extraire les IDs des suggestions (exclure l'utilisateur lui-même)
matches = [int(df.iloc[i]['id']) for i in indices[0] if df.iloc[i]['id'] != user_id]

# 🔹 Retourner les résultats en JSON
print(json.dumps({"user_id": user_id, "matches": matches}, ensure_ascii=False).encode("utf-8").decode("utf-8"))
