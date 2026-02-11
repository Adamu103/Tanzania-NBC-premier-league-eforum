<?php
session_start();
include "db2.php";

if(!isset($_SESSION['user_id'])){
    exit();
}

$user_id = $_SESSION['user_id'];

function timeAgo($datetime){
    $time = strtotime($datetime);
    $diff = time() - $time;

    if($diff < 60) return $diff." sec ago";
    if($diff < 3600) return floor($diff/60)." min ago";
    if($diff < 86400) return floor($diff/3600)." hrs ago";
    return floor($diff/86400)." days ago";
}

$action = $_GET['action'] ?? '';

/* ================= FETCH POSTS ================= */
if($action == "fetch"){

    $search = $_GET['search'] ?? '';
    $team   = $_GET['team'] ?? '';
    $date   = $_GET['date'] ?? '';
    $sort   = $_GET['sort'] ?? '';

    $search = $conn->real_escape_string($search);

    $query = "SELECT posts.*,users.username,users.profile_pic,
    (SELECT COUNT(*) FROM reactions WHERE post_id=posts.post_id AND type='like') as likes,
    (SELECT COUNT(*) FROM reactions WHERE post_id=posts.post_id AND type='dislike') as dislikes,
    (SELECT COUNT(*) FROM comments WHERE post_id=posts.post_id) as comments
    FROM posts 
    JOIN users ON posts.user_id=users.user_id
    WHERE posts.title LIKE '%$search%'";

    if($team != "")
        $query .= " AND posts.team='$team'";

    if($date == "today")
        $query .= " AND DATE(posts.created_at)=CURDATE()";

    if($date == "week")
        $query .= " AND YEARWEEK(posts.created_at)=YEARWEEK(NOW())";

    if($sort == "oldest")
        $query .= " ORDER BY posts.created_at ASC";
    elseif($sort == "most_liked")
        $query .= " ORDER BY likes DESC";
    else
        $query .= " ORDER BY posts.created_at DESC";

    $result = $conn->query($query);

    while($row = $result->fetch_assoc()){
?>

<div class="post-card">

<div class="post-header">
<img src="uploads/<?= $row['profile_pic'] ?: 'default.png' ?>" class="profile-pic">
<div>
<strong><?= htmlspecialchars($row['username']) ?></strong>
<small><?= timeAgo($row['created_at']) ?></small>
</div>
</div>

<h4><?= htmlspecialchars($row['title']) ?></h4>
<p><?= htmlspecialchars($row['content']) ?></p>

<?php if(!empty($row['image'])): ?>
<img src="uploads/<?= $row['image'] ?>" class="post-image">
<?php endif; ?>

<div class="actions">

<button onclick="react(<?= $row['post_id'] ?>,'like')">
<span class="material-symbols-outlined">thumb_up</span> 
<span id="like<?= $row['post_id'] ?>"><?= $row['likes'] ?></span>
</button>

<button onclick="react(<?= $row['post_id'] ?>,'dislike')">
<span class="material-symbols-outlined">thumb_down</span> 
<span id="dislike<?= $row['post_id'] ?>"><?= $row['dislikes'] ?></span>
</button>

<span>
<span class="material-symbols-outlined">comment</span> 
<span id="commentCount<?= $row['post_id'] ?>"><?= $row['comments'] ?></span>
</span>

<?php if($row['user_id'] == $user_id): ?>
<button onclick="deletePost(<?= $row['post_id'] ?>)">
<span class="material-symbols-outlined">delete</span>
</button>
<?php endif; ?>

</div>

<div class="comment-box">
<input type="text" placeholder="Write comment..." 
onkeydown="if(event.key==='Enter') addComment(this,<?= $row['post_id'] ?>)">
</div>

</div>

<?php
    }
    exit();
}


/* ================= ADD POST ================= */
if($action == "add"){

    $title   = $_POST['title'];
    $content = $_POST['content'];
    $team    = $_POST['team'];

    $image = "";
    if(!empty($_FILES['image']['name'])){
        $image = time().$_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/".$image);
    }

    $stmt = $conn->prepare("INSERT INTO posts(user_id,title,content,team,image) VALUES(?,?,?,?,?)");
    $stmt->bind_param("issss",$user_id,$title,$content,$team,$image);
    $stmt->execute();
    exit();
}


/* ================= REACT ================= */
if($action == "react"){

    $post_id = $_POST['post_id'];
    $type    = $_POST['type'];

    $conn->query("DELETE FROM reactions WHERE post_id=$post_id AND user_id=$user_id");

    $stmt = $conn->prepare("INSERT INTO reactions(post_id,user_id,type) VALUES(?,?,?)");
    $stmt->bind_param("iis",$post_id,$user_id,$type);
    $stmt->execute();

    exit();
}


/* ================= DELETE POST ================= */
if($action == "delete"){

    $post_id = $_POST['post_id'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE post_id=? AND user_id=?");
    $stmt->bind_param("ii",$post_id,$user_id);
    $stmt->execute();
    exit();
}


/* ================= COMMENT ================= */
if($action == "comment"){

    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO comments(post_id,user_id,comment) VALUES(?,?,?)");
    $stmt->bind_param("iis",$post_id,$user_id,$comment);
    $stmt->execute();

    exit();
}
?>

