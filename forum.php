<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

$page_num = max(1, (int)input('page', 1));
$per_page = 15;

$total_count = (int)db_row('SELECT COUNT(*) c FROM post')['c'];
$pag         = paginate($total_count, $page_num, $per_page);

$posts = db_query("
    SELECT 
        p.post_id, p.title, p.created_at, 
        u.user_name,
        (SELECT COUNT(*) FROM comment c WHERE c.post_id = p.post_id) AS comment_count,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') - 
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'downvote') AS score,
        (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id) AS tags
    FROM post p
    JOIN user u ON p.user_id = u.user_id
    ORDER BY p.created_at DESC
    LIMIT {$per_page} OFFSET {$pag['offset']}
");

$page_title = 'Community Forum';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="section-title mb-1"><i class="bi bi-chat-square-text me-2 text-accent"></i>Community Forum</h1>
            <p class="text-muted mb-0">Discuss PC builds, hardware news, and ask for advice.</p>
        </div>
        <div>
            <a href="<?= BASE_URL ?>/forum_create.php" class="btn btn-accent"><i class="bi bi-pencil-square me-1"></i>New Post</a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="list-group list-group-flush rounded">
            <?php if (empty($posts)): ?>
                <div class="list-group-item p-5 text-center text-muted">
                    <i class="bi bi-chat-dots display-4 mb-3 d-block"></i>
                    <h5>No posts found</h5>
                    <p>Be the first to start a discussion!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <a href="<?= BASE_URL ?>/forum_post.php?id=<?= $post['post_id'] ?>" class="list-group-item list-group-item-action p-4">
                    <div class="d-flex gap-3">
                        <div class="text-center" style="min-width: 60px;">
                            <div class="fs-4 fw-bold text-accent"><?= (int)$post['score'] ?></div>
                            <small class="text-muted">votes</small>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1 text-light fw-600"><?= sanitise($post['title']) ?></h5>
                            <?php if (!empty($post['tags'])): ?>
                                <div class="mt-2 mb-2 d-flex gap-2 flex-wrap">
                                    <?php foreach (explode(',', $post['tags']) as $t): ?>
                                    <span class="badge bg-secondary">#<?= sanitise($t) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <div class="d-flex align-items-center gap-3 text-muted small mt-2">
                                <span><i class="bi bi-person me-1"></i><?= sanitise($post['user_name']) ?></span>
                                <span><i class="bi bi-clock me-1"></i><?= date('M j, Y g:i A', strtotime($post['created_at'])) ?></span>
                                <span><i class="bi bi-chat me-1"></i><?= (int)$post['comment_count'] ?> comments</span>
                            </div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="mt-4">
        <?php render_pagination($pag, BASE_URL . '/forum.php?'); ?>
    </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
