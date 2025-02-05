import axios from 'axios';

// Function to convert file to base64
const loadImageBase64 = (file) => {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = (error) => reject(error);
    });
};

// Listen to file input change event
document.getElementById('image-upload').addEventListener('change', async (event) => {
    const file = event.target.files[0];  // Get the selected file
    const image = await loadImageBase64(file);  // Convert to base64

    // Send to Roboflow
    axios({
        method: "POST",
        url: "https://detect.roboflow.com/recyclable-items/3",  // Replace with your API URL
        params: {
            api_key: "API_KEY"  // Replace with your actual API key
        },
        data: image,
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        }
    })
    .then(response => {
        console.log(response.data);  // Handle successful inference
    })
    .catch(error => {
        console.log(error.message);  // Handle errors
    });
});
