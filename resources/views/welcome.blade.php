<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Task Manager Pro</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    color:#e0f7fa;
    min-height:100vh;
}
.container-box {
    max-width:600px;
    margin:auto;
    margin-top:40px;
    background: rgba(39, 117, 128, 0.85);
    padding:20px;
    border-radius:18px;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.6);
    animation: fadeIn 0.8s ease;
    transition: 0.3s;
}
.container-box:hover {
    transform: scale(1.02);
}
input {
    margin-bottom:10px;
    background:#1c3b40;
    border:none;
    color:#fff;
    transition:0.3s;
}
input:focus {
    background:#276f78;
    box-shadow:0 0 10px #4dd0e1;
}
button {
    transition:0.3s !important;
}
button:hover {
    transform: scale(1.05);
    opacity:0.9;
}
.task {
    background: rgba(20, 80, 90, 0.7);
    padding:12px;
    border-radius:12px;
    margin-bottom:12px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    animation: slideUp 0.4s ease;
    transition: 0.3s;
}
.task:hover {
    transform: translateY(-6px) scale(1.02);
    background: rgba(39, 117, 128, 0.95);
    box-shadow: 0 5px 15px rgba(0,0,0,0.4);
}
.task button {
    margin-left:5px;
}
h2, h3 {
    color:#b2ebf2;
}
p {
    color:#b0bec5;
}
.switch-link {
    cursor:pointer;
    color:#4dd0e1;
    transition:0.3s;
}
.switch-link:hover {
    text-decoration:underline;
    color:#b2ebf2;
}
@keyframes fadeIn {
    from {opacity:0; transform:translateY(10px);}
    to {opacity:1; transform:translateY(0);}
}
@keyframes slideUp {
    from {
        opacity:0;
        transform:translateY(20px);
    }
    to {
        opacity:1;
        transform:translateY(0);
    }
}
@media (max-width: 500px) {
    .container-box {
        margin:20px;
    }
    .task {
        flex-direction:column;
        align-items:flex-start;
    }
    .task div:last-child {
        margin-top:10px;
    }
}
</style>
</head>
<body>
<div id="loginPage" class="container-box text-center">
    <h2>Login</h2>
    <input type="email" id="loginEmail" class="form-control" placeholder="Email">
    <input type="password" id="loginPassword" class="form-control" placeholder="Password">
    <button onclick="login()" class="btn btn-warning w-100 mt-2">Login</button>
    <p class="mt-3">
        Don't have an account?
        <span class="switch-link" onclick="showRegister()">Register</span>
    </p>
</div>
<div id="registerPage" class="container-box text-center" style="display:none;">
    <h2>Register</h2>
    <input type="text" id="name" class="form-control" placeholder="Name">
    <input type="email" id="registerEmail" class="form-control" placeholder="Email">
    <input type="password" id="registerPassword" class="form-control" placeholder="Password">
    <button onclick="register()" class="btn btn-success w-100 mt-2">Register</button>
    <p class="mt-3">
        Already have an account?
        <span class="switch-link" onclick="showLogin()">Login</span>
    </p>
</div>
<div id="app" class="container-box" style="display:none;">

    <div class="d-flex justify-content-between mb-3">
        <h3>Task Manager</h3>
        <button onclick="logout()" class="btn btn-danger btn-sm">Logout</button>
    </div>
    <input type="text" id="title" class="form-control" placeholder="Task Title">
    <input type="text" id="description" class="form-control" placeholder="Task Description">
    <button onclick="addTask()" class="btn btn-primary w-100">+ Add Task</button>

        <div class="mt-3">
        <p>Total: <span id="total">0</span></p>
        <p>Completed: <span id="completed">0</span></p>
        <p>Pending: <span id="pending">0</span></p>
    </div>
    <div id="taskList"></div>
</div>
<script>
const API_URL = "http://127.0.0.1:8000/api/tasks";
let TOKEN = "";
function showRegister(){
    document.getElementById("loginPage").style.display="none";
    document.getElementById("registerPage").style.display="block";
}
function showLogin(){
    document.getElementById("registerPage").style.display="none";
    document.getElementById("loginPage").style.display="block";
}
function register(){
    fetch("http://127.0.0.1:8000/api/register",{
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body:JSON.stringify({
            name:document.getElementById("name").value,
            email:document.getElementById("registerEmail").value,
            password:document.getElementById("registerPassword").value
        })
    })
    .then(res=>res.json())
    .then(data=>{
        alert("Registered Successfully ✅");
        showLogin();
    });
}
function login(){
    fetch("http://127.0.0.1:8000/api/login",{
        method:"POST",
        headers:{ "Content-Type":"application/json" },
        body:JSON.stringify({
            email:document.getElementById("loginEmail").value,
            password:document.getElementById("loginPassword").value
        })
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.token){
            TOKEN=data.token;
            localStorage.setItem("token",TOKEN);

            document.getElementById("loginPage").style.display="none";
            document.getElementById("app").style.display="block";
            getTasks();
        } else {
            alert("Login Failed");
        }
    });
}
function logout(){
    localStorage.removeItem("token");
    location.reload();
}
/* GET TASKS */
function getTasks(){
    fetch(API_URL,{
        headers:{ "Authorization":"Bearer "+TOKEN }
    })
    .then(res=>res.json())
    .then(data=>{
        const list=document.getElementById("taskList");
        list.innerHTML="";
        let total=0,completed=0;
        data.forEach(task=>{
            total++;
            if(task.is_completed) completed++;
            const div=document.createElement("div");
            div.className="task";
            div.innerHTML=`
                <div>
                    <strong>${task.title}</strong><br>
                    ${task.description}
                </div>
                <div>
                    <button class="btn btn-success btn-sm" onclick="toggle(${task.id},${task.is_completed})">
                        ${task.is_completed?"Undo":"Done"}
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">X</button>
                </div>
            `;
            list.appendChild(div);
        });
        document.getElementById("total").innerText=total;
        document.getElementById("completed").innerText=completed;
        document.getElementById("pending").innerText=total-completed;
    });
}
function addTask(){
    fetch(API_URL,{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "Authorization":"Bearer "+TOKEN
        },
        body:JSON.stringify({
            title:document.getElementById("title").value,
            description:document.getElementById("description").value
        })
    })
    .then(()=>getTasks());
}
function deleteTask(id){
    fetch(API_URL+"/"+id,{
        method:"DELETE",
        headers:{ "Authorization":"Bearer "+TOKEN }
    })
    .then(()=>getTasks());
}
function toggle(id,status){
    fetch(API_URL+"/"+id,{
        method:"PATCH",
        headers:{
            "Content-Type":"application/json",
            "Authorization":"Bearer "+TOKEN
        },
        body:JSON.stringify({ is_completed:!status })
    })
    .then(()=>getTasks());
}
window.onload=()=>{
    const t=localStorage.getItem("token");
    if(t){
        TOKEN=t;
        document.getElementById("loginPage").style.display="none";
        document.getElementById("app").style.display="block";
        getTasks();
    }
}
</script>
</body>
</html>