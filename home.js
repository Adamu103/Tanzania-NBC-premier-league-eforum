document.addEventListener("DOMContentLoaded",function(){

loadPosts();

document.getElementById("search").onkeyup=loadPosts;
document.getElementById("team").onchange=loadPosts;
document.getElementById("date").onchange=loadPosts;
document.getElementById("sort").onchange=loadPosts;

document.getElementById("postForm").onsubmit=function(e){
e.preventDefault();
let data=new FormData(this);
fetch("ajax.php?action=add",{method:"POST",body:data})
.then(()=>{this.reset();loadPosts();});
};

});

function loadPosts(){
let search=document.getElementById("search").value;
let team=document.getElementById("team").value;
let date=document.getElementById("date").value;
let sort=document.getElementById("sort").value;

fetch(`ajax.php?action=fetch&search=${search}&team=${team}&date=${date}&sort=${sort}`)
.then(res=>res.text())
.then(data=>document.getElementById("posts").innerHTML=data);
}

function react(id,type){
fetch("ajax.php?action=react",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`post_id=${id}&type=${type}`
}).then(loadPosts);
}

function deletePost(id){
fetch("ajax.php?action=delete",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`post_id=${id}`
}).then(loadPosts);
}

function addComment(input,id){
let text=input.value;
if(text.trim()=="") return;

fetch("ajax.php?action=comment",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:`post_id=${id}&comment=${text}`
}).then(()=>{
input.value="";
loadPosts();
});
}
