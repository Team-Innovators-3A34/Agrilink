from flask import Flask, request, jsonify
from flask_cors import CORS
import joblib
import pandas as pd

app = Flask(__name__)
CORS(app)  # Activer CORS pour toutes les origines

# Charger le modèle
model = joblib.load("crop_recommendation_model.pkl")

# Définition des colonnes attendues
columns = ["N", "P", "K", "temperature", "humidity", "ph", "rainfall"]

@app.route('/predict', methods=['POST'])
def predict():
    try:
        data = request.get_json()
        df = pd.DataFrame([data], columns=columns)
        prediction = model.predict(df)[0]
        return jsonify({"recommended_crop": prediction})
    except Exception as e:
        return jsonify({"error": str(e)}), 400

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5001, debug=True)
