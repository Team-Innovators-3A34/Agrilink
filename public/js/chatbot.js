// public/js/chatbot.js

document.getElementById('chatbotButton').addEventListener('click', function() {
    document.getElementById('chatbotPopup').style.display = 'block';
});

document.getElementById('closeChatbot').addEventListener('click', function() {
    document.getElementById('chatbotPopup').style.display = 'none';
});

document.getElementById('sendMessage').addEventListener('click', function() {
    var userMessage = document.getElementById('chatbotInput').value;
    if (userMessage) {
        // Display user message
        displayMessage(userMessage, 'user');
        
        // Send message to server (replace '/chat' with your backend endpoint)
        fetch('/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: userMessage })
        })
        .then(response => response.json())
        .then(data => {
            // Display chatbot response
            displayMessage(data.response, 'bot');
        })
        .catch(error => console.error('Error:', error));
        
        // Clear input field
        document.getElementById('chatbotInput').value = '';
    }
});

// Function to display messages in the chat window
function displayMessage(message, sender) {
    var messageDiv = document.createElement('div');
    messageDiv.classList.add(sender);
    messageDiv.innerText = message;
    document.getElementById('chatbotMessages').appendChild(messageDiv);
}
