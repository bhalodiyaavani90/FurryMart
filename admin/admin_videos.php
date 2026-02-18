<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

// 1. DELETE PROTOCOL
if(isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM pet_moods WHERE id=$id");
    header("Location: admin_videos.php?success=deleted");
}

// 2. UPLOAD/UPDATE PROTOCOL
if(isset($_POST['save_video'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $id = (int)$_POST['video_id'];

    if(!empty($_FILES['video_file']['name'])) {
        $file_name = time() . "_" . $_FILES['video_file']['name'];
        move_uploaded_file($_FILES['video_file']['tmp_name'], "../uploads/videos/" . $file_name);
        $video_sql = ", video_url='$file_name'";
    } else { $video_sql = ""; }

    if($id > 0) {
        $sql = "UPDATE pet_moods SET title='$title', category='$cat', description='$desc' $video_sql WHERE id=$id";
    } else {
        $sql = "INSERT INTO pet_moods (title, category, video_url, description) VALUES ('$title', '$cat', '$file_name', '$desc')";
    }
    mysqli_query($conn, $sql);
    header("Location: admin_videos.php?success=saved");
}

$edit_v = ['id'=>0, 'title'=>'', 'category'=>'Happy', 'description'=>''];
if(isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit_v = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pet_moods WHERE id=$eid"));
}

include('includes/header.php');
?>

<style>
    .admin-video-wrapper {
        padding: 30px 4%;
        background: linear-gradient(135deg, #f8fafc 0%, #e7f3f5 100%);
        min-height: 100vh;
    }
    .video-header-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        color: white;
        padding: 35px 40px;
        border-radius: 25px;
        margin-bottom: 35px;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.2);
        position: relative;
        overflow: hidden;
    }
    .video-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(81, 137, 146, 0.15) 0%, transparent 70%);
        border-radius: 50%;
    }
    .video-header-card h2 {
        font-size: 32px;
        font-weight: 900;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .video-header-card h2 i {
        font-size: 38px;
        color: #22c55e;
    }
    .video-header-card p {
        margin: 0;
        opacity: 0.85;
        font-size: 15px;
    }
    
    .video-form-card {
        background: white;
        padding: 35px;
        border-radius: 25px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        margin-bottom: 40px;
        border: 1px solid #e2e8f0;
    }
    .video-form-card h3 {
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 25px 0;
        padding-bottom: 15px;
        border-bottom: 3px solid #518992;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 20px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
    }
    .form-group label {
        font-weight: 800;
        font-size: 13px;
        text-transform: uppercase;
        color: #475569;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 14px 18px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        font-size: 15px;
        font-family: inherit;
        transition: all 0.3s;
        background: #f8fafc;
    }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #518992;
        background: white;
        box-shadow: 0 0 0 4px rgba(81, 137, 146, 0.1);
    }
    .form-group input[type="file"] {
        border-style: dashed;
        border-color: #cbd5e1;
        padding: 20px;
        cursor: pointer;
    }
    .form-group input[type="file"]:hover {
        border-color: #518992;
        background: #f0f9fa;
    }
    .form-group textarea {
        height: 120px;
        resize: vertical;
    }
    .btn-save-video {
        width: 100%;
        padding: 18px;
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
        color: white;
        border: none;
        border-radius: 15px;
        font-weight: 900;
        font-size: 15px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s;
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
        margin-top: 20px;
    }
    .btn-save-video:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 35px rgba(34, 197, 94, 0.4);
    }
    .btn-save-video:active {
        transform: translateY(-1px);
    }
    
    .video-table-card {
        background: white;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        border: 1px solid #e2e8f0;
    }
    .video-table-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 25px 30px;
        color: white;
    }
    .video-table-header h3 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .video-table {
        width: 100%;
        border-collapse: collapse;
    }
    .video-table thead {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
    }
    .video-table thead th {
        padding: 18px 20px;
        text-align: left;
        font-weight: 800;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .video-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.3s;
    }
    .video-table tbody tr:hover {
        background: #f8fafc;
    }
    .video-table tbody td {
        padding: 20px;
        font-size: 14px;
    }
    .category-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-happy { background: #fef3c7; color: #ca8a04; }
    .badge-sad { background: #dbeafe; color: #1d4ed8; }
    .badge-playful { background: #fce7f3; color: #db2777; }
    .badge-grateful { background: #dcfce7; color: #15803d; }
    
    .status-synced {
        color: #16a34a;
        font-weight: 800;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .status-synced i {
        font-size: 16px;
    }
    .action-buttons {
        display: flex;
        gap: 12px;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 15px;
    }
    .btn-edit {
        background: #e0f2fe;
        color: #0369a1;
    }
    .btn-edit:hover {
        background: #0369a1;
        color: white;
        transform: translateY(-2px);
    }
    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }
    .btn-delete:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    .success-message {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #15803d;
        padding: 15px 25px;
        border-radius: 15px;
        margin-bottom: 25px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 2px solid #86efac;
    }
    .success-message i {
        font-size: 20px;
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-video-wrapper">
    <?php if(isset($_GET['success'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i>
            <span><?php echo $_GET['success'] == 'deleted' ? 'Video deleted successfully!' : 'Video saved successfully!'; ?></span>
        </div>
    <?php endif; ?>

    <div class="video-header-card">
        <h2><i class="fas fa-video"></i> Pet Feelings Video Manager</h2>
        <p>Manage emotional content library for your pet community</p>
    </div>

    <div class="video-form-card">
        <h3><i class="fas fa-plus-circle"></i> <?php echo $edit_v['id'] > 0 ? 'Edit Video' : 'Add New Video'; ?></h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="video_id" value="<?php echo $edit_v['id']; ?>">
            
            <div class="form-grid">
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Video Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($edit_v['title']); ?>" required placeholder="Enter video title...">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-smile"></i> Mood Category</label>
                    <select name="category">
                        <option value="Happy" <?php echo $edit_v['category']=='Happy'?'selected':''; ?>>😊 Happy</option>
                        <option value="Sad" <?php echo $edit_v['category']=='Sad'?'selected':''; ?>>😢 Sad</option>
                        <option value="Playful" <?php echo $edit_v['category']=='Playful'?'selected':''; ?>>🎮 Playful</option>
                        <option value="Grateful" <?php echo $edit_v['category']=='Grateful'?'selected':''; ?>>🙏 Grateful</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-file-video"></i> Video File (MP4)</label>
                <input type="file" name="video_file" accept="video/*">
                <?php if($edit_v['id'] > 0): ?>
                    <small style="color: #64748b; margin-top: 8px; display: block;">Leave empty to keep existing video</small>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" placeholder="Add a brief description about this video..."><?php echo htmlspecialchars($edit_v['description']); ?></textarea>
            </div>
            
            <button type="submit" name="save_video" class="btn-save-video">
                <i class="fas fa-save"></i> <?php echo $edit_v['id'] > 0 ? 'Update Video' : 'Save Video'; ?>
            </button>
        </form>
    </div>

    <div class="video-table-card">
        <div class="video-table-header">
            <h3><i class="fas fa-list"></i> Video Library (<?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM pet_moods")); ?> Videos)</h3>
        </div>
        <table class="video-table">
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <th><i class="fas fa-video"></i> Video Title</th>
                    <th><i class="fas fa-tag"></i> Category</th>
                    <th><i class="fas fa-check-circle"></i> Status</th>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res = mysqli_query($conn, "SELECT * FROM pet_moods ORDER BY id DESC");
                if(mysqli_num_rows($res) > 0):
                    while($r = mysqli_fetch_assoc($res)): 
                        $badge_class = 'badge-happy';
                        if($r['category'] == 'Sad') $badge_class = 'badge-sad';
                        elseif($r['category'] == 'Playful') $badge_class = 'badge-playful';
                        elseif($r['category'] == 'Grateful') $badge_class = 'badge-grateful';
                ?>
                <tr>
                    <td style="font-weight: 700; color: #64748b;">#<?php echo $r['id']; ?></td>
                    <td style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($r['title']); ?></td>
                    <td><span class="category-badge <?php echo $badge_class; ?>"><?php echo $r['category']; ?></span></td>
                    <td><span class="status-synced"><i class="fas fa-check-circle"></i> Active</span></td>
                    <td>
                        <div class="action-buttons">
                            <a href="?edit=<?php echo $r['id']; ?>" class="btn-action btn-edit" title="Edit Video">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $r['id']; ?>" class="btn-action btn-delete" title="Delete Video" 
                               onclick="return confirm('Are you sure you want to delete this video?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 50px; color: #94a3b8;">
                        <i class="fas fa-video" style="font-size: 50px; opacity: 0.3; display: block; margin-bottom: 15px;"></i>
                        <strong>No videos found</strong><br>
                        <small>Start by adding your first video above</small>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>