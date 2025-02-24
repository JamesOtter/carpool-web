<!-- Chatbot Button and Container -->
<div id="chatbot-container" class="fixed bottom-8 right-8 z-50">
    <!-- Chatbot Icon Button -->
    <button id="chatbot-toggle" class="bg-blue-600 text-white p-3 rounded-full shadow-lg transition-transform duration-300 hover:scale-105">
        <x-bladewind::icon name="chat-bubble-left-ellipsis" />
    </button>

    <!-- Chatbot Window (Hidden by Default) -->
    <div id="chatbot-box" class="hidden fixed bottom-24 right-5 w-80 bg-white rounded-lg shadow-xl border border-gray-300">
        <!-- Chatbot Header -->
        <div class="flex justify-between items-center bg-blue-600 text-white p-3 rounded-t-lg">
            <span class="font-bold">Ride Finder Bot</span>
            <button id="chatbot-close" class="text-xl"><x-bladewind::icon name="x-circle" /></button>
        </div>

        <!-- Chat Messages -->
        <div id="chatbot-content" class="h-64 overflow-y-auto p-3 bg-gray-100 flex flex-col space-y-2">
            <!-- Message wrapper -->
            <div class="flex justify-start gap-1">
                <x-bladewind::icon name="light-bulb" class="p-1 bg-yellow-500 text-white border-2 rounded-full"/>
                <!-- Bubble wrapper -->
                <div class="bg-gray-200 text-gray-900 p-2 rounded-lg max-w-xs text-sm shadow-md">
                    <p class="text-sm text-gray-700">Hello! How can I assist you?</p>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-3 border-t flex">
            <input type="text" id="chatbot-input" class="w-full border rounded px-2 py-1" placeholder="Type a message...">
            <button id="chatbot-send" class="ml-2 bg-blue-600 text-white px-4 py-1 rounded border-yellow-500">
                <x-bladewind::icon name="paper-airplane" />
            </button>
        </div>
    </div>
</div>

<!-- Chatbot Script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatbotToggle = document.getElementById("chatbot-toggle");
        const chatbotBox = document.getElementById("chatbot-box");
        const chatbotClose = document.getElementById("chatbot-close");
        const chatbotInput = document.getElementById("chatbot-input");
        const chatbotContent = document.getElementById("chatbot-content");
        const chatbotSend = document.getElementById("chatbot-send");

        // Restore chatbot state
        if (sessionStorage.getItem("chatbotOpen") === "true") {
            chatbotBox.classList.remove("hidden");
        }

        // Toggle chatbot visibility
        chatbotToggle.addEventListener("click", function () {
            chatbotBox.classList.toggle("hidden");
            sessionStorage.setItem("chatbotOpen", !chatbotBox.classList.contains("hidden"));
        });

        // Close chatbot
        chatbotClose.addEventListener("click", function () {
            chatbotBox.classList.add("hidden");
            sessionStorage.setItem("chatbotOpen", "false");
        });

        // Send message when button is clicked
        chatbotSend.addEventListener("click", sendMessage);

        // Send message on Enter key press
        chatbotInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") sendMessage();
        });

        // Function to send a message
        async function sendMessage() {
            const inputField = document.getElementById("chatbot-input");
            const message = inputField.value.trim();
            if (message === "") return;

            appendMessage("You", message);
            saveChatHistory();
            inputField.value = "";

            try {
                let response = await fetch("/api/chatbot", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: message })
                });

                let data = await response.json();
                if (data && data.fulfillmentMessages) {
                    appendMessage("Bot", data.fulfillmentMessages[0].text.text[0]);
                    saveChatHistory();
                }
            } catch (error) {
                console.error("Error communicating with chatbot:", error);
                appendMessage("Bot", "Sorry, something went wrong.");
                saveChatHistory();
            }
        }

        // Function to append a message to the chat
        function appendMessage(sender, message) {
            const messageWrapper = document.createElement("div");
            messageWrapper.className = sender === "You" ? "flex justify-end mb-2" : "flex justify-start mb-2";

            const messageBubble = document.createElement("div");
            messageBubble.className = sender === "You"
                ? "bg-blue-500 text-white p-2 rounded-lg max-w-xs text-sm shadow-md"
                : "bg-gray-200 text-gray-900 p-2 rounded-lg max-w-xs text-sm shadow-md";

            messageBubble.innerHTML = `${message}`;

            const chatIcon = document.createElement("div");
            chatIcon.innerHTML = sender === "You"
                ? `<x-bladewind::icon name="user" class="p-1 bg-blue-500 text-white border-2 rounded-full border-blue-500"/>`
                : `<x-bladewind::icon name="light-bulb" class="p-1 bg-yellow-500 text-white border-2 rounded-full border-yellow-500"/>`
            ;

            const conversation = document.createElement("div");
            conversation.className = sender === "You"
                ? "flex flex-row-reverse gap-1"
                : "flex gap-1"
            ;

            conversation.appendChild(chatIcon);
            conversation.appendChild(messageBubble);
            messageWrapper.appendChild(conversation);
            chatbotContent.appendChild(messageWrapper);
            chatbotContent.scrollTop = chatbotContent.scrollHeight;
        }

        // Save chat history
        function saveChatHistory() {
            sessionStorage.setItem("chatHistory", chatbotContent.innerHTML);
        }

        // Load chat history
        function loadChatHistory() {
            const savedChat = sessionStorage.getItem("chatHistory");
            if (savedChat) {
                chatbotContent.innerHTML = savedChat;
            }
        }

        loadChatHistory();
    });
</script>

