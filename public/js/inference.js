import axios from 'axios';

// Function to load image as Base64
const loadImageBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
}

// Image upload and inference
const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    try {
        const imageBase64 = await loadImageBase64(file);

        axios({
            method: "POST",
            url: "https://detect.roboflow.com/recyclable-items/3",
            params: {
                api_key: "rf_AjxPJrV7k8MXyZCJyxY0amP3TfP2"
            },
            data: imageBase64,
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        })
        .then(function(response) {
            console.log(response.data);
        })
        .catch(function(error) {
            console.log(error.message);
        });
    } catch (error) {
        console.error("Error processing image:", error);
    }
}

// Add event listener to the file input
document.getElementById("imageInput").addEventListener("change", handleImageUpload);
