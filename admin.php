<?php
session_start();
require "db.php";

/* ====== CHECK ADMIN ====== */
/* Hakikisha user ana role = admin kwenye users table */
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "admin") {
    header("Location: login.php");
    exit();
}

/* ====== DELETE POST ====== */
if(isset($_GET["delete_post"])){
    $post_id = $_GET["delete_post"];

    $conn->query("DELETE FROM posts WHERE id='$post_id'");
    $conn->query("DELETE FROM comments WHERE post_id='$post_id'");
    $conn->query("DELETE FROM likes WHERE post_id='$post_id'");

    header("Location: admin.php");
}

/* ====== DELETE COMMENT ====== */
if(isset($_GET["delete_comment"])){
    $comment_id = $_GET["delete_comment"];

    $conn->query("DELETE FROM comments WHERE id='$comment_id'");
    header("Location: admin.php");
}

/* ====== STATS ====== */
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()["total"];
$totalPosts = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()["total"];
$totalComments = $conn->query("SELECT COUNT(*) as total FROM comments")->fetch_assoc()["total"];
$totalLikes = $conn->query("SELECT COUNT(*) as total FROM likes")->fetch_assoc()["total"];

$posts = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Panel - NBC eForum</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI, sans-serif;}

body{
background:linear-gradient(rgba(0,0,0,0.8),rgba(0,0,0,0.8)),
url('background1.png') no-repeat center center/cover;
min-height:100vh;
color:white;
}

header{
display:flex;
justify-content:space-between;
align-items:center;
padding:20px 40px;
background:rgba(255,255,255,0.1);
backdrop-filter:blur(10px);
}

header h2{
display:flex;
align-items:center;
gap:10px;
}

header a{
color:white;
text-decoration:none;
display:flex;
align-items:center;
gap:5px;
background:#ff9800;
padding:8px 15px;
border-radius:8px;
}

.container{
width:90%;
margin:30px auto;
}

.stats{
display:grid;
grid-template-columns:repeat(4,1fr);
gap:20px;
margin-bottom:30px;
}

.stat-card{
background:rgba(255,255,255,0.1);
padding:20px;
border-radius:15px;
text-align:center;
backdrop-filter:blur(15px);
box-shadow:0 8px 25px rgba(0,0,0,0.4);
}

.stat-card h3{
margin-top:10px;
font-size:22px;
}

.card{
background:rgba(255,255,255,0.1);
padding:20px;
border-radius:15px;
margin-bottom:20px;
backdrop-filter:blur(15px);
}

.post-header{
display:flex;
justify-content:space-between;
align-items:center;
}

.delete-btn{
color:red;
text-decoration:none;
display:flex;
align-items:center;
gap:5px;
}

.comment-box{
margin-top:10px;
padding:10px;
background:rgba(255,255,255,0.1);
border-radius:10px;
display:flex;
justify-content:space-between;
}
</style>
</head>

<body>

<header>
<h2><span class="material-icons">admin_panel_settings</span> Admin Dashboard</h2>
<a href="home.php"><span class="material-icons">arrow_back</span> Rudi Home</a>
</header>

<div class="container">

<!-- ===== STATS ===== -->
<div class="stats">

<div class="stat-card">
<span class="material-icons">people</span>
<h3><?php echo $totalUsers; ?></h3>
<p>Users</p>
</div>

<div class="stat-card">
<span class="material-icons">forum</span>
<h3><?php echo $totalPosts; ?></h3>
<p>Posts</p>
</div>

<div class="stat-card">
<span class="material-icons">comment</span>
<h3><?php echo $totalComments; ?></h3>
<p>Comments</p>
</div>

<div class="stat-card">
<span class="material-icons">thumb_up</span>
<h3><?php echo $totalLikes; ?></h3>
<p>Likes</p>
</div>

</div>

<!-- ===== POSTS MANAGEMENT ===== -->

<?php while($row = $posts->fetch_assoc()){ ?>

<div class="card">

<div class="post-header">
<div>
<b><?php echo $row["username"]; ?></b><br>
<small><?php echo $row["created_at"]; ?></small>
</div>

<a href="admin.php?delete_post=<?php echo $row["id"]; ?>" class="delete-btn">
<span class="material-icons">delete</span> Delete Post
</a>
</div>

<p style="margin-top:10px;"><?php echo $row["content"]; ?></p>

<?php if(!empty($row["image"])){ ?>
<img src="uploads/<?php echo $row["image"]; ?>" style="width:100%;margin-top:10px;border-radius:10px;">
<?php } ?>

<!-- COMMENTS -->
<?php
$post_id = $row["id"];
$comments = $conn->query("SELECT * FROM comments WHERE post_id='$post_id'");
while($c = $comments->fetch_assoc()){
?>

<div class="comment-box">
<div>
<b><?php echo $c["username"]; ?></b><br>
<?php echo $c["comment"]; ?>
</div>

<a href="admin.php?delete_comment=<?php echo $c["id"]; ?>" class="delete-btn">
<span class="material-icons">delete</span>
</a>
</div>

<?php } ?>

</div>

<?php } ?>

</div>

</body>
</html>

