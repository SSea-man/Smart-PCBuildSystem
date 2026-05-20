<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();

$error = '';
$title = '';
$content = '';

if (is_post()) {
    verify_csrf();
    $title   = trim(input('title', ''));
    $content = trim(input('content', ''));
    $tags_input = trim(input('tags', ''));
    
    if (strlen($title) < 5 || strlen($title) > 100) {
        $error = 'Title must be between 5 and 100 characters.';
    } elseif (strlen($content) < 10) {
        $error = 'Post content is too short.';
    } else {
        $user_id = get_auth_user()['id'];
        db_exec('INSERT INTO post (user_id, title, content, created_at) VALUES (?, ?, ?, NOW())', [$user_id, $title, $content]);
        $post_id = db_row('SELECT LAST_INSERT_ID() AS id')['id'];
        
        // Handle tags
        if ($tags_input) {
            $tags = array_unique(array_filter(array_map('trim', explode(',', strtolower($tags_input)))));
            foreach ($tags as $t) {
                if (strlen($t) > 0 && strlen($t) <= 50) {
                    $existing_tag = db_row('SELECT tag_id FROM tag WHERE name = ?', [$t]);
                    if ($existing_tag) {
                        $tag_id = $existing_tag['tag_id'];
                    } else {
                        db_exec('INSERT INTO tag (name) VALUES (?)', [$t]);
                        $tag_id = db_row('SELECT LAST_INSERT_ID() AS id')['id'];
                    }
                    db_exec('INSERT INTO posttag (post_id, tag_id, created_at) VALUES (?, ?, NOW())', [$post_id, $tag_id]);
                }
            }
        }
        
        flash_message('success', 'Your post has been published.');
        redirect('forum_post.php?id=' . $post_id);
    }
}

$page_title = 'Create New Post';
include __DIR__ . '/templates/header.php';
?>
<div class="container-xl py-4" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="<?= BASE_URL ?>/forum.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Forum</a>
        <h1 class="section-title mb-0 flex-grow-1"><i class="bi bi-pencil-square me-2 text-accent"></i>Create New Post</h1>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i><?= sanitise($error) ?></div>
            <?php endif; ?>

            <form method="POST">
                <?php csrf_field(); ?>
                
                <div class="mb-3">
                    <label for="title" class="form-label fw-600">Post Title</label>
                    <input type="text" class="form-control" id="title" name="title" required minlength="5" maxlength="100" 
                           placeholder="What do you want to discuss?" value="<?= sanitise($title) ?>">
                    <div class="form-text">Keep it clear and descriptive (5-100 characters).</div>
                </div>

                <div class="mb-4">
                    <label for="content" class="form-label fw-600">Content</label>
                    <textarea class="form-control" id="content" name="content" rows="10" required minlength="10" 
                              placeholder="Share your thoughts, ask a question, or post a build..."><?= sanitise($content) ?></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="tags" class="form-label fw-600">Tags (optional)</label>
                    <input type="text" class="form-control" id="tags" name="tags" 
                           placeholder="e.g. build, gpu, budget">
                    <div class="form-text">Comma-separated keywords.</div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="<?= BASE_URL ?>/forum.php" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-accent"><i class="bi bi-send me-2"></i>Publish Post</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/templates/footer.php'; ?>
