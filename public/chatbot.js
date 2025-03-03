document.addEventListener("DOMContentLoaded", function () {
    const chatbotIcon = document.getElementById("chatbot-icon");
    const chatbotModal = document.getElementById("chatbot");
    const closeChatbot = document.getElementById("close-chatbot");
    const chatInput = document.getElementById("chat-input");
    const chatSend = document.getElementById("chat-send");
    const chatWindow = document.getElementById("chat-window");

    // Ouvrir le chatbot
    chatbotIcon.addEventListener("click", function () {
        chatbotModal.style.display = "block";
    });

    // Fermer le chatbot
    closeChatbot.addEventListener("click", function (event) {
        event.preventDefault();
        chatbotModal.style.display = "none";
    });

    // Fonction pour envoyer un message
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Afficher le message de l'utilisateur
        chatWindow.innerHTML += `
            <div class="message self text-right mt-2">
                <div class="message-content font-xssss lh-24 fw-500">${message}</div>
            </div>
        `;

        // Effacer le champ input
        chatInput.value = "";

        // Afficher le chargement
        chatWindow.innerHTML += `
            <div class="snippet pt-3 ps-4 pb-2 pe-3 mt-2 bg-grey rounded-xl float-right" id="loading">
                <div class="stage"><div class="dot-typing"></div></div>
            </div>
        `;

        // Envoyer la requête à l'API Symfony
        fetch("/api/chatbot", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            const loadingElement = document.getElementById("loading");
            if (loadingElement) loadingElement.remove(); // Supprimer le chargement

            // Afficher la réponse du chatbot
            chatWindow.innerHTML += `
                <div class="message">
                    <div class="message-content font-xssss lh-24 fw-500">${data.response}</div>
                </div>
            `;
            chatWindow.scrollTop = chatWindow.scrollHeight; // Faire défiler vers le bas
        })
        .catch(error => {
            const loadingElement = document.getElementById("loading");
            if (loadingElement) loadingElement.remove();
            console.error("Erreur du chatbot :", error);
        });
    }

    // Envoyer un message en cliquant sur le bouton
    chatSend.addEventListener("click", sendMessage);

    // Envoyer un message en appuyant sur "Entrée"
    chatInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            sendMessage();
        }
    });
});
