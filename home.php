<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

/* ================= POST ================= */
if (isset($_POST["submit"])) {

    $content = $_POST["content"];
    $team = $_POST["team"];
    $username = $_SESSION["username"];
    $imageName = "";

    if (!empty($_FILES["image"]["name"])) {
        $imageName = time() . "_" . $_FILES["image"]["name"];
        move_uploaded_file($_FILES["image"]["tmp_name"], "uploads/" . $imageName);
    }

    $stmt = $conn->prepare("INSERT INTO posts (content, image, team, username, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $content, $imageName, $team, $username);
    $stmt->execute();
    header("Location: home.php");
}

/* ================= LIKE ================= */
if(isset($_GET["like"])){
    $post_id = $_GET["like"];
    $user_id = $_SESSION["user_id"];

    $check = $conn->query("SELECT * FROM likes WHERE post_id='$post_id' AND user_id='$user_id'");
    if($check->num_rows == 0){
        $conn->query("INSERT INTO likes (post_id,user_id) VALUES ('$post_id','$user_id')");
    }

    header("Location: home.php");
}

/* ================= DELETE (OWNER ONLY) ================= */
if(isset($_GET["delete"])){
    $post_id = $_GET["delete"];
    $username = $_SESSION["username"];

    $conn->query("DELETE FROM posts WHERE id='$post_id' AND username='$username'");
    header("Location: home.php");
}

/* ================= COMMENT ================= */
if(isset($_POST["comment_submit"])){
    $post_id = $_POST["post_id"];
    $comment = $_POST["comment"];
    $username = $_SESSION["username"];

    $stmt = $conn->prepare("INSERT INTO comments (post_id, username, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iss",$post_id,$username,$comment);
    $stmt->execute();
    header("Location: home.php");
}

/* ================= NAVIGATION ================= */
$page = isset($_GET["page"]) ? $_GET["page"] : "home";

/* ================= SEARCH ================= */
$search = "";
$teamFilter = "";

$sql = "SELECT * FROM posts WHERE 1";

if (!empty($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " AND content LIKE '%$search%'";
}

if (!empty($_GET['team'])) {
    $teamFilter = $_GET['team'];
    $sql .= " AND team='$teamFilter'";
}

$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>NBC eForum</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:Segoe UI, sans-serif;}

body{
background:linear-gradient(rgba(0,0,0,0.7),rgba(0,0,0,0.7)),
url('background1.png') no-repeat center center/cover;
min-height:100vh;
color:white;
}

header{
display:flex;
justify-content:space-between;
align-items:center;
padding:15px 40px;
background:rgba(255,255,255,0.1);
backdrop-filter:blur(10px);
}

header img{
width:60px;height:60px;border-radius:50%;
}

nav{
display:flex;gap:20px;
}

nav a{
color:white;
text-decoration:none;
display:flex;
align-items:center;
gap:5px;
padding:8px 15px;
border-radius:8px;
transition:0.3s;
}

nav a:hover{
background:#ff9800;
}

.active{
background:#ff9800;
}

.container{
width:85%;
margin:30px auto;
}

.search-box{
display:flex;
gap:15px;
margin-bottom:20px;
}

.input-group{
display:flex;
align-items:center;
background:rgba(255,255,255,0.15);
padding:10px;
border-radius:10px;
backdrop-filter:blur(10px);
}

.input-group input,
.input-group select{
background:transparent;
border:none;
outline:none;
color:white;
margin-left:10px;
}

button{
background:#ff9800;
border:none;
padding:10px 20px;
border-radius:10px;
cursor:pointer;
display:flex;
align-items:center;
gap:5px;
}

.card{
background:rgba(255,255,255,0.1);
padding:20px;
border-radius:15px;
margin-bottom:20px;
backdrop-filter:blur(15px);
box-shadow:0 8px 25px rgba(0,0,0,0.4);
}

textarea{
width:100%;
height:100px;
padding:10px;
border-radius:10px;
border:none;
margin-bottom:10px;
}

.post-header{
display:flex;
gap:15px;
align-items:center;
margin-bottom:15px;
}

.profile{
width:50px;height:50px;border-radius:50%;
}

.post-img{
width:100%;
margin-top:15px;
border-radius:10px;
}

.team{
margin-top:10px;
color:#ffcc00;
display:flex;
align-items:center;
gap:5px;
}

.team-card{
padding:30px;
text-align:center;
font-size:20px;
}

.no-post{text-align:center;margin-top:40px;font-size:18px;}
</style>
</head>

<body>

<header>
<img src="image.jpg">

<nav>
<a href="home.php?page=home" class="<?php if($page=='home') echo 'active'; ?>">
<span class="material-icons">home</span> Home
</a>

<a href="home.php?page=teams" class="<?php if($page=='teams') echo 'active'; ?>">
<span class="material-icons">groups</span> Teams
</a>

<a href="login.php">
<span class="material-icons">logout</span> Logout
</a>
</nav>
</header>

<div class="container">

<?php if($page=="home"){ ?>

<form method="GET" class="search-box">
<input type="hidden" name="page" value="home">

<div class="input-group">
<span class="material-icons">search</span>
<input type="text" name="search" placeholder="Tafuta mjadala..." value="<?php echo $search; ?>">
</div>

<div class="input-group">
<span class="material-icons">filter_list</span>
<select name="team">
<option value="">Chuja kwa Timu</option>
<option value="Simba" <?php if($teamFilter=="Simba") echo "selected"; ?>>Simba</option>
<option value="Yanga" <?php if($teamFilter=="Yanga") echo "selected"; ?>>Yanga</option>
<option value="Azam" <?php if($teamFilter=="Azam") echo "selected"; ?>>Azam</option>
</select>
</div>

<button type="submit">
<span class="material-icons">manage_search</span> Tafuta
</button>
</form>

<div class="card">
<form method="POST" enctype="multipart/form-data">
<textarea name="content" placeholder="Andika discussion yako hapa..." required></textarea>

<select name="team" required>
<option value="">Chagua Timu</option>
<option value="Simba">Simba</option>
<option value="Yanga">Yanga</option>
<option value="Azam">Azam</option>
</select>

<input type="file" name="image" accept="image/*">

<button type="submit" name="submit">
<span class="material-icons">send</span> Post
</button>
</form>
</div>

<?php
if($result->num_rows>0){
while($row=$result->fetch_assoc()){
$post_id = $row["id"];
$likeCount = $conn->query("SELECT COUNT(*) as total FROM likes WHERE post_id='$post_id'")->fetch_assoc()["total"];
$commentCount = $conn->query("SELECT COUNT(*) as total FROM comments WHERE post_id='$post_id'")->fetch_assoc()["total"];
?>

<div class="card">
<div class="post-header">
<img src="default.png" class="profile">
<div>
<b><?php echo $row["username"]; ?></b><br>
<small><?php echo $row["created_at"]; ?></small>
</div>
</div>

<p><?php echo $row["content"]; ?></p>

<?php if(!empty($row["image"])){ ?>
<img src="uploads/<?php echo $row["image"]; ?>" class="post-img">
<?php } ?>

<div class="team">
<span class="material-icons">groups</span>
<?php echo $row["team"]; ?>
</div>

<div style="margin-top:15px; display:flex; gap:20px; align-items:center;">

<a href="home.php?like=<?php echo $post_id; ?>" style="color:white;text-decoration:none;display:flex;align-items:center;gap:5px;">
<span class="material-icons">thumb_up</span>
<?php echo $likeCount; ?>
</a>

<span style="display:flex;align-items:center;gap:5px;">
<span class="material-icons">comment</span>
<?php echo $commentCount; ?>
</span>

<?php if($_SESSION["username"] == $row["username"]){ ?>
<a href="home.php?delete=<?php echo $post_id; ?>" style="color:red;text-decoration:none;display:flex;align-items:center;gap:5px;">
<span class="material-icons">delete</span>
Delete
</a>
<?php } ?>
</div>

<div style="margin-top:15px;">

<form method="POST" style="display:flex;gap:10px;">
<input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
<input type="text" name="comment" placeholder="Andika comment..." required
style="flex:1;padding:8px;border-radius:8px;border:none;">
<button type="submit" name="comment_submit">
<span class="material-icons">send</span>
</button>
</form>

<?php
$comments = $conn->query("SELECT * FROM comments WHERE post_id='$post_id' ORDER BY created_at DESC");
while($c = $comments->fetch_assoc()){
?>
<div style="margin-top:10px;background:rgba(255,255,255,0.1);padding:8px;border-radius:8px;">
<b><?php echo $c["username"]; ?></b><br>
<?php echo $c["comment"]; ?>
</div>
<?php } ?>

</div>

</div>

<?php
}
}else{
echo "<p class='no-post'>Hakuna posts zilizopatikana</p>";
}
?>

<?php } elseif($page=="teams"){ ?>

<div class="card team-card">
<h2><span class="material-icons">groups</span> Teams Section</h2>
<p>Simba | Yanga | Azam</p>

</div>

<?php } ?>

</div>
</body>
</html>

