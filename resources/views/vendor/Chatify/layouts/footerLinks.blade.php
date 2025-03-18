<style>
        /* Styles for modal */
        .modal {
            display: none; /* Initially hidden */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 400px;
            text-align: center;
            animation: fadeIn 0.3s ease-in-out;
            left: 35%;
            top: 35%;
            position: absolute;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close:hover {
            color: #000;
        }

        h2 {
            margin-bottom: 15px;
            font-size: 22px;
        }

        input[type="text"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        #userSelection {
            text-align: left;
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin: 5px 0;
        }

        button {
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        button:hover {
            background: #0056b3;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
<div id="createGroupModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Создать группу</h2>
        <input type="text" id="groupName" placeholder="Название группы">
        <p>Выберите пользователей:</p>
        <div id="userSelection">
    @foreach($users as $user)
        <label>
            <input type="checkbox" value="{{ $user->id }}"> {{ $user->name }}
        </label>
    @endforeach
</div>

        <button id="createGroupSubmit">Создать</button>
    </div>
</div>
<script>
        document.addEventListener("DOMContentLoaded", function () {

    const modal = document.getElementById("createGroupModal");

    modal.style.display = "none";
        });
        
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("createGroupModal");
    const btn = document.querySelector(".create-group-btn");
    const span = document.querySelector(".close");
    const submitBtn = document.getElementById("createGroupSubmit");

    btn.onclick = function () {
        modal.style.display = "block";
    };
 
    span.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    };


    submitBtn.onclick = function () {
        const groupName = document.getElementById("groupName").value;
        const selectedUsers = [...document.querySelectorAll("#userSelection input:checked")].map(el => el.value);

        if (!groupName || selectedUsers.length === 0) {
            alert("Введите название группы и выберите хотя бы одного пользователя");
            return;
        }

        fetch("/chatify/create-group", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            body: JSON.stringify({ name: groupName, members: selectedUsers })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            modal.style.display = "none";
            location.reload();
        })
        .catch(error => console.error("Ошибка:", error));
    };
});

</script>

<script src="https://js.pusher.com/7.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@3.0.3/dist/index.min.js"></script>
<script >
    // Gloabl Chatify variables from PHP to JS
    window.chatify = {
        name: "{{ config('chatify.name') }}",
        sounds: {!! json_encode(config('chatify.sounds')) !!},
        allowedImages: {!! json_encode(config('chatify.attachments.allowed_images')) !!},
        allowedFiles: {!! json_encode(config('chatify.attachments.allowed_files')) !!},
        maxUploadSize: {{ Chatify::getMaxUploadSize() }},
        pusher: {!! json_encode(config('chatify.pusher')) !!},
        pusherAuthEndpoint: '{{route("pusher.auth")}}'
    };
    window.chatify.allAllowedExtensions = chatify.allowedImages.concat(chatify.allowedFiles);
</script>
<script src="{{ asset('js/chatify/utils.js') }}"></script>
<script src="{{ asset('js/chatify/code.js') }}"></script>
