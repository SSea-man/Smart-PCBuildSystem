<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();

$post_id = (int)input('id', 0);
if (!$post_id) redirect('forum.php');

$user_id = is_logged_in() ? get_auth_user()['id'] : 0;

$post = db_row("
    SELECT p.*, u.user_name, c.name AS community_name,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
    FROM post p
    JOIN user u ON p.user_id = u.user_id
    LEFT JOIN community c ON p.community_id = c.community_id
    WHERE p.post_id = ?
", [$user_id, $post_id]);

if (!$post) {
    flash_message('danger', 'Post not found.');
    redirect('forum.php');
}

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

$comments = db_query("
    SELECT c.*, u.user_name,
        (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.vote_type = 'upvote') AS score,
        (SELECT COUNT(*) FROM vote v WHERE v.comment_id = c.comment_id AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
    FROM comment c
    JOIN user u ON c.user_id = u.user_id
    WHERE c.post_id = ?
    ORDER BY c.created_at ASC
", [$user_id, $post_id]);

$tags = db_query("
    SELECT t.name 
    FROM tag t 
    JOIN posttag pt ON t.tag_id = pt.tag_id 
    WHERE pt.post_id = ?
", [$post_id]);

$comm_details = null;
if ($post['community_id']) {
    $comm_details = db_row("
        SELECT c.community_id, c.name, c.description, c.created_at,
               (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
               (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?) AS is_joined
        FROM community c
        WHERE c.community_id = ?
    ", [$user_id, $post['community_id']]);
}

$page_title = $post['title'];
include __DIR__ . '/templates/header.php';
?>
<style>
.forum-bold-text {
    color: var(--text-primary) !important;
    font-weight: 700;
}
.forum-link-text {
    color: var(--text-primary) !important;
    text-decoration: none;
    transition: color var(--transition);
}
.forum-link-text:hover {
    color: var(--accent) !important;
}

.reddit-layout-grid {
    display: grid;
    grid-template-columns: 240px 1fr 312px;
    gap: 24px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 24px 15px;
}

@media (max-width: 992px) {
    .reddit-layout-grid {
        grid-template-columns: 220px 1fr;
    }
    .reddit-right-sidebar {
        display: none;
    }
}

@media (max-width: 768px) {
    .reddit-layout-grid {
        grid-template-columns: 1fr;
    }
    .reddit-left-sidebar {
        display: none;
    }
}

.reddit-sidebar-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-secondary);
    letter-spacing: 0.5px;
    margin-bottom: 12px;
    padding: 0 12px;
}
.reddit-sidebar-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border-radius: 30px;
    transition: all var(--transition);
}
.reddit-sidebar-link:hover {
    background-color: var(--accent-soft);
    color: var(--accent);
}
.reddit-sidebar-link.active {
    background-color: var(--accent-soft);
    font-weight: 700;
    color: var(--accent) !important;
}
.reddit-left-sidebar hr {
    border-color: var(--border);
    margin: 16px 0;
}

.reddit-post-card {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 24px;
    margin-bottom: 20px;
}
.reddit-post-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 12px;
}
.reddit-post-header a {
    color: var(--text-secondary);
    text-decoration: none;
}
.reddit-post-header a:hover {
    text-decoration: underline;
    color: var(--accent);
}
.reddit-post-title {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    line-height: 1.3;
    font-family: var(--font-head);
}
.reddit-post-content {
    color: var(--text-primary);
    font-size: 1rem;
    opacity: 0.9;
    line-height: 1.6;
    margin-bottom: 16px;
    white-space: pre-wrap;
}
.reddit-post-media {
    border-radius: var(--radius-sm);
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.2);
    border: 1px solid var(--border);
    margin-bottom: 16px;
    max-height: 600px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.reddit-post-media img {
    max-width: 100%;
    max-height: 600px;
    object-fit: contain;
}

.reddit-comments-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 16px;
    font-family: var(--font-head);
}
.reddit-comment-box {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 24px;
}
.reddit-comment-card {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 16px;
    margin-bottom: 12px;
    transition: border-color var(--transition);
}
.reddit-comment-card:hover {
    border-color: var(--accent);
}
.reddit-comment-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 8px;
}
.reddit-comment-content {
    color: var(--text-primary);
    font-size: 0.95rem;
    line-height: 1.5;
    white-space: pre-wrap;
    margin-bottom: 12px;
}

.avatar-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: bold;
    font-size: 0.75rem;
}

.tag-badge {
    background: var(--accent-soft);
    border: 1px solid rgba(16, 185, 129, 0.15);
    color: var(--accent);
    border-radius: 20px;
    padding: 0.3rem 0.8rem;
    font-size: 0.8rem;
    transition: all var(--transition);
}
.tag-badge:hover {
    background: var(--accent);
    color: #fff;
}

.reddit-actions-row {
    display: flex;
    align-items: center;
    gap: 8px;
}
.vote-pill {
    background: var(--bg-input) !important;
    border: 1px solid var(--border);
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    padding: 2px 4px;
}
.vote-pill .vote-btn {
    background: none;
    border: none;
    color: var(--text-secondary);
    padding: 6px 12px;
    font-size: 1rem;
    cursor: pointer;
    transition: all var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.vote-pill .vote-btn:hover {
    color: var(--accent);
}
.vote-pill .vote-btn.active {
    color: var(--accent) !important;
    font-weight: bold;
}
.vote-pill .vote-btn.active i::before {
    content: "\f415"; }
.vote-score {
    color: var(--text-primary);
    font-weight: 700;
    font-size: 0.85rem;
    padding-right: 8px;
}
.vote-score.upvoted {
    color: var(--accent);
}

.action-pill {
    background: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 999px;
    color: var(--text-primary);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--transition);
}
.action-pill:hover {
    background: var(--bg-card-hover);
    border-color: var(--accent);
    color: var(--accent);
}

.reddit-right-card {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 16px;
}
.reddit-right-card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-secondary);
    text-transform: uppercase;
    margin-bottom: 12px;
}

.btn-join-toggle {
    font-size: 0.8rem;
    font-weight: 700;
    border-radius: 999px;
    padding: 6px 16px;
    transition: all var(--transition);
}
.btn-join-toggle.joined {
    background-color: transparent;
    border: 1px solid var(--accent);
    color: var(--accent);
}
.btn-join-toggle.joined:hover {
    background-color: var(--accent-soft);
}
.btn-join-toggle.not-joined {
    background-color: var(--accent);
    border: 1px solid var(--accent);
    color: #fff;
}
.btn-join-toggle.not-joined:hover {
    background-color: var(--accent-hover);
    border-color: var(--accent-hover);
}
</style>

<div class="reddit-layout-grid">
    <div class="reddit-left-sidebar">
        <a href="<?= BASE_URL ?>/forum.php" class="reddit-sidebar-link">
            <i class="bi bi-arrow-left fs-5"></i>Back to Feed
        </a>
        <a href="<?= BASE_URL ?>/forum.php?tab=discussions" class="reddit-sidebar-link">
            <i class="bi bi-house-door fs-5"></i>Home / Discussions
        </a>
        <a href="<?= BASE_URL ?>/forum.php?tab=trending" class="reddit-sidebar-link">
            <i class="bi bi-fire fs-5"></i>Popular / Trending
        </a>
        <a href="<?= BASE_URL ?>/forum.php?tab=announcements" class="reddit-sidebar-link">
            <i class="bi bi-megaphone fs-5"></i>Announcements
        </a>
        
        <hr>
        
        <div class="reddit-sidebar-title">Resources</div>
        <a href="<?= BASE_URL ?>/store.php" class="reddit-sidebar-link">
            <i class="bi bi-shop fs-5"></i>Component Store
        </a>
        <a href="<?= BASE_URL ?>/purpose.php" class="reddit-sidebar-link">
            <i class="bi bi-magic fs-5"></i>PC Build Wizard
        </a>
        <a href="<?= BASE_URL ?>/chatbot.php" class="reddit-sidebar-link">
            <i class="bi bi-robot fs-5"></i>AI PC Assistant
        </a>
    </div>

    <div class="reddit-center-feed">
        <div class="reddit-post-card">
            <div class="reddit-post-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-circle" style="width: 24px; height: 24px; font-size: 0.55rem; font-weight: 700; background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">
                        pcb/
                    </div>
                    <span class="fw-bold forum-bold-text">
                        <?php if (!empty($post['community_name'])): ?>
                            <a href="forum.php?community_id=<?= $post['community_id'] ?>" class="forum-link-text">pcb/<?= sanitise($post['community_name']) ?></a>
                        <?php else: ?>
                            <a href="forum.php" class="forum-link-text">pcb/PCBuilderBD</a>
                        <?php endif; ?>
                    </span>
                    <span>•</span>
                    <span>Posted by <span class="text-primary-emphasis">u/<?= sanitise($post['user_name']) ?></span></span>
                    <span>•</span>
                    <span><?= date('M j, Y, g:i a', strtotime($post['created_at'])) ?></span>
                </div>
            </div>

            <h1 class="reddit-post-title"><?= sanitise($post['title']) ?></h1>

            <?php if (!empty($post['image_path'])): ?>
            <div class="reddit-post-media">
                <img src="<?= BASE_URL ?>/<?= sanitise($post['image_path']) ?>" alt="Post image">
            </div>
            <?php endif; ?>

            <div class="reddit-post-content"><?= sanitise($post['content']) ?></div>

            <?php if (!empty($tags)): ?>
            <div class="mb-4 d-flex gap-2 flex-wrap">
                <?php foreach ($tags as $t): ?>
                <span class="tag-badge"><?= sanitise($t['name']) ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="reddit-actions-row">
                <div class="vote-pill" data-post-id="<?= $post['post_id'] ?>">
                    <button class="vote-btn upvote-btn <?= $post['user_vote'] > 0 ? 'active' : '' ?>" 
                            data-type="post" data-id="<?= $post['post_id'] ?>">
                        <i class="bi bi-heart"></i>
                        <span class="vote-score <?= $post['user_vote'] > 0 ? 'upvoted' : '' ?>" id="score-post-<?= $post['post_id'] ?>"><?= (int)$post['score'] ?></span>
                    </button>
                </div>

                <span class="action-pill">
                    <i class="bi bi-chat-left-text"></i>
                    <span><?= count($comments) ?> Comments</span>
                </span>

                <a href="#" class="action-pill share-btn" data-url="<?= BASE_URL ?>/forum_post.php?id=<?= $post['post_id'] ?>">
                    <i class="bi bi-share"></i>
                    <span>Share</span>
                </a>

                <?php if (is_logged_in() && (is_moderator() || is_admin() || (int)$post['user_id'] === (int)$user_id)): ?>
    <button type="button" onclick="forumDeletePost(<?= $post['post_id'] ?>, this)" class="action-pill text-danger" style="background: rgba(220,53,69,0.1); border-color: rgba(220,53,69,0.2);">
        <i class="bi bi-trash"></i> Delete Post
    </button>
<?php endif; ?>
            </div>
        </div>

        <?php if (is_logged_in()): ?>
        <div class="reddit-comment-box">
            <h5 class="fw-bold mb-3">Comment as <span class="text-accent">u/<?= sanitise(get_auth_user()['username'] ?? get_auth_user()['email']) ?></span></h5>
            <form method="POST">
                <?php csrf_field(); ?>
                <div class="mb-3">
                    <textarea class="form-control text-light" name="content" rows="4" required placeholder="What are your thoughts?" style="background-color: var(--bg-input); border-color: var(--border);"></textarea>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-accent px-4 py-2 rounded-pill">
                        <i class="bi bi-chat-fill me-1"></i>Comment
                    </button>
                </div>
            </form>
        </div>
        <?php else: ?>
        <div class="alert alert-secondary text-center mb-4" style="background-color: var(--bg-card); border-color: var(--border);">
            Please <a href="<?= BASE_URL ?>/login.php" class="fw-bold text-accent">log in</a> to comment or vote.
        </div>
        <?php endif; ?>

        <h4 class="reddit-comments-title px-1"><i class="bi bi-chat-left-dots-fill text-accent me-2"></i>Comments (<?= count($comments) ?>)</h4>
        
        <?php if (empty($comments)): ?>
            <div class="reddit-comment-card text-center p-5 text-muted" style="background-color: var(--bg-card); border-color: var(--border);">No comments yet. Be the first to share your thoughts!</div>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
            <div class="reddit-comment-card">
                <div class="reddit-comment-header">
                    <div class="avatar-circle" style="width: 20px; height: 20px; font-size: 0.65rem; background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">
                        <?= strtoupper(substr($comment['user_name'], 0, 1)) ?>
                    </div>
                    <span class="fw-bold text-white">u/<?= sanitise($comment['user_name']) ?></span>
                    <span>•</span>
                    <span><?= date('M j, Y, g:i a', strtotime($comment['created_at'])) ?></span>
                </div>
                
                <div class="reddit-comment-content"><?= sanitise($comment['content']) ?></div>

                <div class="d-flex align-items-center justify-content-between">
                    <div class="vote-pill">
                        <button class="vote-btn upvote-btn <?= $comment['user_vote'] > 0 ? 'active' : '' ?>" 
                                data-type="comment" data-id="<?= $comment['comment_id'] ?>">
                            <i class="bi bi-heart"></i>
                            <span class="vote-score <?= $comment['user_vote'] > 0 ? 'upvoted' : '' ?>" id="score-comment-<?= $comment['comment_id'] ?>"><?= (int)$comment['score'] ?></span>
                        </button>
                    </div>
                    <?php if (is_logged_in() && (is_moderator() || is_admin() || (int)$comment['user_id'] === (int)$user_id)): ?>
                    <button type="button" onclick="forumDeleteComment(<?= $comment['comment_id'] ?>, this)" class="btn btn-link text-danger text-decoration-none btn-sm">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="reddit-right-sidebar">
        <div class="reddit-right-card">
            <?php if ($comm_details): ?>
                <div class="reddit-right-card-title">About pcb/<?= sanitise($comm_details['name']) ?></div>
                <p class="text-muted small mb-3" style="line-height: 1.5;">
                    <?= sanitise($comm_details['description'] ?: 'Welcome to the ' . $comm_details['name'] . ' community! Let us share our custom PC builds, tips, and guidelines.') ?>
                </p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="forum-bold-text d-block" id="sidebar-members-count" style="font-size: 1.1rem;"><?= number_format($comm_details['member_count']) ?></span>
                        <span class="text-muted small">Members</span>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-join-toggle btn-join-action <?= $comm_details['is_joined'] ? 'joined' : 'not-joined' ?>" 
                                data-id="<?= $comm_details['community_id'] ?>">
                            <?= $comm_details['is_joined'] ? 'Joined' : 'Join' ?>
                        </button>
                    </div>
                </div>
                <hr style="border-color: var(--border); margin: 15px 0;">
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Created</span>
                    <span class="forum-bold-text small"><?= date('M j, Y', strtotime($comm_details['created_at'])) ?></span>
                </div>
            <?php else: ?>
                <div class="reddit-right-card-title">About pcb/PCBuilderBD</div>
                <p class="text-muted small mb-3" style="line-height: 1.5;">
                    Welcome to Bangladesh's premium PC Builder community! Connect with builders, ask advice, get component deals, and showcase your builds.
                </p>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Created</span>
                    <span class="forum-bold-text small">May 19, 2026</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Topic</span>
                    <span class="forum-bold-text small">PC Hardware / Gaming</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="reddit-right-card">
            <div class="reddit-right-card-title">pcb/PCBuilderBD Rules</div>
            <ol class="ps-3 mb-0 text-muted small" style="line-height: 1.6;">
                <li class="mb-2"><span class="forum-bold-text">Be Respectful:</span> No harassment, hate speech, or toxicity.</li>
                <li class="mb-2"><span class="forum-bold-text">Keep it PC Related:</span> Discussions, memes, and builds must be relevant to hardware/gaming.</li>
                <li class="mb-2"><span class="forum-bold-text">No Spam or Self-Promo:</span> Keep advertising out of public threads.</li>
                <li><span class="forum-bold-text">Use Appropriate Tags:</span> Label your builds, tech support, and news properly.</li>
            </ol>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.vote-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!window.IS_LOGGED_IN) {
                alert("You must be logged in to vote.");
                return;
            }
            const targetType = btn.dataset.type; 
            const targetId = btn.dataset.id;
            
            try {
                const fd = new FormData();
                fd.append('type', targetType);
                fd.append('id', targetId);
                fd.append('csrf_token', window.CSRF_TOKEN);
                
                const res = await fetch(`${window.BASE_URL}/api/vote.php`, {
                    method: 'POST',
                    body: fd
                });
                
                const data = await res.json();
                if (data.success) {
                    const scoreEl = document.getElementById(`score-${targetType}-${targetId}`);
                    scoreEl.innerText = data.new_score;
                    
                    if (data.user_voted) {
                        btn.classList.add('active');
                        scoreEl.classList.add('upvoted');
                    } else {
                        btn.classList.remove('active');
                        scoreEl.classList.remove('upvoted');
                    }
                } else {
                    alert(data.error || "An error occurred.");
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    const btnJoinAction = document.querySelector('.btn-join-action');
    if (btnJoinAction) {
        btnJoinAction.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!window.IS_LOGGED_IN) {
                alert("You must be logged in to join communities.");
                return;
            }
            const communityId = btnJoinAction.dataset.id;
            
            try {
                const fd = new FormData();
                fd.append('community_id', communityId);
                fd.append('csrf_token', window.CSRF_TOKEN);
                
                const res = await fetch(`${window.BASE_URL}/api/join_community.php`, {
                    method: 'POST',
                    body: fd
                });
                
                const data = await res.json();
                if (data.success) {
                    if (data.joined) {
                        btnJoinAction.classList.remove('not-joined');
                        btnJoinAction.classList.add('joined');
                        btnJoinAction.innerText = 'Joined';
                    } else {
                        btnJoinAction.classList.remove('joined');
                        btnJoinAction.classList.add('not-joined');
                        btnJoinAction.innerText = 'Join';
                    }
                    
                    const sidebarCountEl = document.getElementById('sidebar-members-count');
                    if (sidebarCountEl) {
                        sidebarCountEl.innerText = data.member_count.toLocaleString();
                    }
                } else {
                    alert(data.error || "An error occurred.");
                }
            } catch (err) {
                console.error(err);
            }
        });
    }

    document.querySelectorAll('.share-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const url = btn.dataset.url;
            navigator.clipboard.writeText(url).then(() => {
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check-lg text-success"></i> <span class="text-success">Copied!</span>';
                setTimeout(() => {
                    btn.innerHTML = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
            });
        });
    });

});

async function forumDeletePost(postId, btn) {
    if (!window.IS_LOGGED_IN) { alert('You must be logged in.'); return; }
    if (!window.confirm('Are you sure you want to delete this post? This cannot be undone.')) return;

    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Deleting...';

    try {
        const fd = new FormData();
        fd.append('type', 'post');
        fd.append('id', postId);
        fd.append('csrf_token', window.CSRF_TOKEN);

        const res  = await fetch(window.BASE_URL + '/api/delete_forum_item.php', { method: 'POST', body: fd });
        const text = await res.text();
        console.log('[DELETE POST] id=' + postId + ' status=' + res.status + ' body=' + text);

        let data;
        try { data = JSON.parse(text); } catch(e) {
            alert('Server error: ' + text.substring(0, 200));
            btn.disabled = false; btn.innerHTML = origHTML; return;
        }

        if (data.success) {
            window.location.href = window.BASE_URL + '/forum.php';
        } else {
            alert('Delete failed: ' + (data.error || 'Unknown error'));
            btn.disabled = false; btn.innerHTML = origHTML;
        }
    } catch (err) {
        alert('Network error: ' + err.message);
        btn.disabled = false; btn.innerHTML = origHTML;
    }
}

async function forumDeleteComment(commentId, btn) {
    if (!window.IS_LOGGED_IN) { alert('You must be logged in.'); return; }
    if (!window.confirm('Delete this comment?')) return;

    const origHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i>';

    try {
        const fd = new FormData();
        fd.append('type', 'comment');
        fd.append('id', commentId);
        fd.append('csrf_token', window.CSRF_TOKEN);

        const res  = await fetch(window.BASE_URL + '/api/delete_forum_item.php', { method: 'POST', body: fd });
        const text = await res.text();
        console.log('[DELETE COMMENT] id=' + commentId + ' status=' + res.status + ' body=' + text);

        let data;
        try { data = JSON.parse(text); } catch(e) {
            alert('Server error: ' + text.substring(0, 200));
            btn.disabled = false; btn.innerHTML = origHTML; return;
        }

        if (data.success) {
            const card = btn.closest('.reddit-comment-card');
            if (card) {
                card.style.transition = 'opacity 0.3s ease, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-10px)';
                setTimeout(() => card.remove(), 350);
            }
        } else {
            alert('Delete failed: ' + (data.error || 'Unknown error'));
            btn.disabled = false; btn.innerHTML = origHTML;
        }
    } catch (err) {
        alert('Network error: ' + err.message);
        btn.disabled = false; btn.innerHTML = origHTML;
    }
}
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>
