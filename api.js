function showNotification(message, type="error"){
    let box = document.createElement("div");
    box.className = "notify " + type;
    box.innerText = message;

    document.body.appendChild(box);

    setTimeout(()=>{
        box.remove();
    },3000);
}

/* ================= REGISTER ================= */
function registerUser(e){
    e.preventDefault();

    const username = document.getElementById("regUsername").value.trim();
    const password = document.getElementById("regPassword").value.trim();
    const confirm = document.getElementById("confirmPassword").value.trim();

    if(password !== confirm){
        showNotification("Passwords do not match");
        return;
    }

    fetch("register.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({username,password})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status === "success"){
            showNotification(data.message,"success");
            setTimeout(()=>{
                window.location.href="login.html";
            },1500);
        }else{
            showNotification(data.message);
        }
    })
    .catch(()=>{
        showNotification("Server error");
    });
}

/* ================= LOGIN ================= */
function loginUser(e){
    e.preventDefault();

    const username = document.getElementById("loginUsername").value.trim();
    const password = document.getElementById("loginPassword").value.trim();

    fetch("login.php",{
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body:JSON.stringify({username,password})
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.status === "success"){
            showNotification("Login successful","success");

            setTimeout(()=>{
                if(data.role === "admin"){
                    window.location.href="admin.html";
                }else{
                    window.location.href="dashboard.html";
                }
            },1000);

        }else{
            showNotification(data.message);
        }
    })
    .catch(()=>{
        showNotification("Server error");
    });
}
