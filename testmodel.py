import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from sklearn.ensemble import RandomForestClassifier
import joblib

# 1️⃣ Charger les données
df = pd.read_csv("weather_data.csv")

# 2️⃣ Encoder la colonne 'alert_level' en valeurs numériques
encoder = LabelEncoder()
df["alert_level"] = encoder.fit_transform(df["alert_level"])

# 3️⃣ Séparer les données en features (X) et labels (y)
X = df.drop(columns=["alert_level", "date"])  # Supprimer la date et l'alerte
y = df["alert_level"]

# 4️⃣ Diviser en train et test
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# 5️⃣ Entraîner le modèle
model = RandomForestClassifier(n_estimators=100, random_state=42)
model.fit(X_train, y_train)

# 6️⃣ Sauvegarder le modèle et l’encoder
joblib.dump(model, "weather_alert_model.pkl")
joblib.dump(encoder, "alert_encoder.pkl")

print("✅ Modèle entraîné et sauvegardé !")
