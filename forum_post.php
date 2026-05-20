<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$post_id = (int)input('id', 0);
if (!$post_id) redirect('forum.php');

$post = db_row("
    SELECT p.*, u.user_name,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') - 
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'downvote') AS score
    FROM post p
    JOIN user u ON p.user_id = u.user_id
    WHERE p.post_id = ?
", [$post_id]);

if (!$post) {
    flash_message('danger', 'Post not found.');
    redirect('forum.php');
}

// Handle comment submission
if (is_post() && is_logged_in()) {
    verify_csrf();
    $content = trim(input('content', ''));
    if (strlen($content) > 0) {
        db_exec('INSERT INTO comment (user_id, post_id, content, created_at) VALUES (?, ?, ?, NOW())', 
            [get_auth_user()['id'], $post_id, $content]);
        flash_message('success', 'Comment added.');
        redirect('forum_post.php?id=' . $post_id);
    }
}

// Fetch comments
$comments = db_query("
    SELECT c.*, u.user_name,
        (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.vote_type = 'upvote') - 
        (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.vote_type = 'downvote') AS score
    FROM comment c
    JOIN user u ON c.user_id = u.user_id
    WHERE c.post_id = ?
    ORDER BY c.created_at ASC
", [$post_id]);

// Fetch tags
$tags = db_query("
    SELECT t.name 
    FROM tag t 
    JOIN posttag pt ON t.tag_id = pt.tag_id 
    WHERE pt.post_id = ?
", [$post_id]);

$page_title = $post['title'];
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4" style="max-width: 900px;">
    <div class="mb-4">
        <a href="<?= BASE_URL ?>/forum.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left me-1"></i>Back to Forum</a>
    </div>

    <!-- Post Details -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4 d-flex gap-4">
            <!-- Vote Sidebar -->
            <div class="d-flex flex-column align-items-center gap-1" style="width: 50px;">
                <button class="btn btn-sm btn-ghost fs-5 vote-btn" data-type="post" data-id="<?= $post_id ?>" data-vote="upvote"><i class="bi bi-caret-up-fill"></i></button>
                <span class="fs-5 fw-bold" id="score-post-<?= $post_id ?>"><?= (int)$post['score'] ?></span>
                <button class="btn btn-sm btn-ghost fs-5 vote-btn" data-type="post" data-id="<?= $post_id ?>" data-vote="downvote"><i class="bi bi-caret-down-fill"></i></button>
            </div>
            
            <!-- Post Content -->
            <div class="flex-grow-1">
                <h1 class="h3 fw-700 mb-2"><?= sanitise($post['title']) ?></h1>
                
                <?php if (!empty($tags)): ?>
                <div class="mb-3 d-flex gap-2 flex-wrap">
                    <?php foreach ($tags as $t): ?>
                    <span class="badge bg-secondary">#<?= sanitise($t['name']) ?></span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="text-muted small mb-4">
                    <i class="bi bi-person me-1"></i><?= sanitise($post['user_name']) ?> &nbsp;&bull;&nbsp; 
                    <i class="bi bi-clock me-1"></i><?= date('F j, Y, g:i a', strtotime($post['created_at'])) ?>
                </div>
                <div class="post-content lh-lg" style="white-space: pre-wrap;"><?= sanitise($post['content']) ?></div>
            </div>
        </div>
    </div>

    <!-- Comments Section -->
    <h4 class="mb-3"><i class="bi bi-chat-text me-2 text-accent"></i><?= count($comments) ?> Comments</h4>
    
    <div class="card shadow-sm border-0 mb-4">
        <div class="list-group list-group-flush rounded">
            <?php foreach ($comments as $comment): ?>
            <div class="list-group-item p-4 d-flex gap-3">
                <!-- Vote Sidebar for Comment -->
                <div class="d-flex flex-column align-items-center gap-0" style="width: 40px;">
                    <button class="btn btn-sm btn-ghost vote-btn" data-type="comment" data-id="<?= $comment['comment_id'] ?>" data-vote="upvote"><i class="bi bi-caret-up-fill"></i></button>
                    <span class="fw-bold small" id="score-comment-<?= $comment['comment_id'] ?>"><?= (int)$comment['score'] ?></span>
                    <button class="btn btn-sm btn-ghost vote-btn" data-type="comment" data-id="<?= $comment['comment_id'] ?>" data-vote="downvote"><i class="bi bi-caret-down-fill"></i></button>
                </div>
                
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between mb-2">
                        <strong class="text-light"><?= sanitise($comment['user_name']) ?></strong>
                        <span class="text-muted small"><?= date('M j, Y, g:i a', strtotime($comment['created_at'])) ?></span>
                    </div>
                    <div class="comment-content" style="white-space: pre-wrap;"><?= sanitise($comment['content']) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Comment Form -->
    <?php if (is_logged_in()): ?>
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="mb-3">Leave a Comment</h5>
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <textarea class="form-control" name="content" rows="4" required placeholder="What are your thoughts?"></textarea>
                </div>
                <button type="submit" class="btn btn-accent"><i class="bi bi-reply-fill me-1"></i>Post Comment</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-secondary text-center">
        Please <a href="<?= BASE_URL ?>/login.php" class="fw-bold text-accent">log in</a> to leave a comment or vote.
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.vote-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            if (!window.IS_LOGGED_IN) {
                alert("You must be logged in to vote.");
                return;
            }
            
            const targetType = btn.dataset.type; // 'post' or 'comment'
            const targetId = btn.dataset.id;
            const voteType = btn.dataset.vote; // 'upvote' or 'downvote'
            
            try {
                const fd = new FormData();
                fd.append('type', targetType);
                fd.append('id', targetId);
                fd.append('vote', voteType);
                fd.append('csrf_token', window.CSRF_TOKEN);
                
                const res = await fetch(`${window.BASE_URL}/api/vote.php`, {
                    method: 'POST',
                    body: fd
                });
                
                const data = await res.json();
                if (data.success) {
                    document.getElementById(`score-${targetType}-${targetId}`).innerText = data.new_score;
                } else {
                    alert(data.error || "An error occurred.");
                }
            } catch (err) {
                console.error(err);
            }
        });
    });
});
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
