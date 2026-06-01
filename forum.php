<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();

$tab = input('tab', 'discussions');
if (!in_array($tab, ['discussions', 'trending', 'announcements'])) {
    $tab = 'discussions';
}

$page_num = max(1, (int)input('page', 1));
$per_page = 15;
$user_id = is_logged_in() ? get_auth_user()['id'] : 0;
$community_id = (int)input('community_id', 0);

$selected_community = null;
$is_joined_selected = false;
if ($community_id) {
    $selected_community = db_row("
        SELECT c.community_id, c.name, c.description,
               (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count
        FROM community c
        WHERE c.community_id = ?
    ", [$community_id]);
    
    if ($selected_community && $user_id) {
        $is_joined_selected = (bool)db_row("
            SELECT 1 FROM community_member 
            WHERE community_id = ? AND user_id = ?
        ", [$community_id, $user_id]);
    }
}

$where_clauses = [];
if ($community_id) {
    $where_clauses[] = "p.community_id = " . $community_id;
}
if ($tab === 'announcements') {
    $where_clauses[] = "EXISTS (SELECT 1 FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id AND t.name = 'announcement')";
}

$where_clause = "";
if (!empty($where_clauses)) {
    $where_clause = "WHERE " . implode(" AND ", $where_clauses);
}

$total_count = (int)db_row("
    SELECT COUNT(*) c 
    FROM post p 
    JOIN user u ON p.user_id = u.user_id 
    {$where_clause}
")['c'];

$pag = paginate($total_count, $page_num, $per_page);

$posts = db_query("
    SELECT 
        p.post_id, p.user_id, p.title, p.content, p.created_at, p.image_path, p.community_id,
        u.user_name,
        c.name AS community_name,
        (SELECT COUNT(*) FROM comment comm WHERE comm.post_id = p.post_id) AS comment_count,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.vote_type = 'upvote') AS score,
        (SELECT GROUP_CONCAT(t.name SEPARATOR ',') FROM posttag pt JOIN tag t ON pt.tag_id = t.tag_id WHERE pt.post_id = p.post_id) AS tags,
        (SELECT COUNT(*) FROM vote v WHERE v.post_id = p.post_id AND v.user_id = ? AND v.vote_type = 'upvote') AS user_vote
    FROM post p
    JOIN user u ON p.user_id = u.user_id
    LEFT JOIN community c ON p.community_id = c.community_id
    {$where_clause}
    ORDER BY " . ($tab === 'trending' ? 'score DESC, ' : '') . "p.created_at DESC
    LIMIT {$per_page} OFFSET {$pag['offset']}
", [$user_id]);

$sidebar_communities = db_query("
    SELECT c.community_id, c.name,
           (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id) AS member_count,
           (SELECT COUNT(*) FROM community_member cm WHERE cm.community_id = c.community_id AND cm.user_id = ?) AS is_joined
    FROM community c
    ORDER BY member_count DESC
    LIMIT 10
", [$user_id]);

$page_title = 'Community Forum';
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

.community-banner {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 20px;
}
.community-banner-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}
.community-banner-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0;
}
.community-banner-meta {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin-top: 4px;
}

.reddit-create-bar {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
}
.reddit-create-input {
    background-color: var(--bg-input);
    border: 1px solid var(--border);
    border-radius: 999px;
    color: var(--text-primary);
    padding: 10px 18px;
    flex-grow: 1;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all var(--transition);
}
.reddit-create-input:hover {
    border-color: var(--accent);
    background-color: var(--bg-card-hover);
}

.reddit-post-card {
    background-color: var(--bg-card);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border: 1px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    margin-bottom: 16px;
    transition: all var(--transition);
}
.reddit-post-card:hover {
    border-color: var(--accent);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.reddit-post-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 10px;
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
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
    line-height: 1.4;
    font-family: var(--font-head);
}
.reddit-post-title:hover {
    color: var(--accent);
}
.reddit-post-content {
    color: var(--text-primary);
    font-size: 0.95rem;
    opacity: 0.9;
    line-height: 1.6;
    margin-bottom: 12px;
}
.reddit-post-media {
    border-radius: var(--radius-sm);
    overflow: hidden;
    background-color: rgba(0, 0, 0, 0.2);
    border: 1px solid var(--border);
    margin-bottom: 12px;
    max-height: 512px;
    display: flex;
    justify-content: center;
    align-items: center;
}
.reddit-post-media img {
    max-width: 100%;
    max-height: 512px;
    object-fit: contain;
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

.modal-content-dark {
    background-color: var(--bg-card);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--border);
    color: var(--text-primary);
}
.modal-header-dark {
    border-bottom: 1px solid var(--border);
}
.modal-footer-dark {
    border-top: 1px solid var(--border);
}
</style>

<div class="reddit-layout-grid">
    <div class="reddit-left-sidebar">
        <a href="?tab=discussions" class="reddit-sidebar-link <?= ($tab === 'discussions' && !$community_id) ? 'active' : '' ?>">
            <i class="bi bi-house-door fs-5"></i>Home / Discussions
        </a>
        <a href="?tab=trending" class="reddit-sidebar-link <?= ($tab === 'trending' && !$community_id) ? 'active' : '' ?>">
            <i class="bi bi-fire fs-5"></i>Popular / Trending
        </a>
        <a href="?tab=announcements" class="reddit-sidebar-link <?= ($tab === 'announcements' && !$community_id) ? 'active' : '' ?>">
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
        <?php if ($selected_community): ?>
        <div class="community-banner">
            <div class="community-banner-header">
                <div>
                    <h1 class="community-banner-title">pcb/<?= sanitise($selected_community['name']) ?></h1>
                    <div class="community-banner-meta">
                        <span id="banner-members-count"><?= number_format($selected_community['member_count']) ?></span> members
                    </div>
                </div>
                <div>
                    <button class="btn btn-join-toggle btn-join-action <?= $is_joined_selected ? 'joined' : 'not-joined' ?>" 
                            data-id="<?= $selected_community['community_id'] ?>">
                        <?= $is_joined_selected ? 'Joined' : 'Join' ?>
                    </button>
                </div>
            </div>
            <?php if (!empty($selected_community['description'])): ?>
            <p class="text-muted small mt-3 mb-0" style="line-height: 1.5; font-size: 0.9rem;">
                <?= sanitise($selected_community['description']) ?>
            </p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="reddit-create-bar">
            <div class="avatar-circle" style="width: 38px; height: 38px; font-size: 0.95rem;">
                <?= is_logged_in() ? strtoupper(substr(get_auth_user()['username'] ?? get_auth_user()['email'], 0, 1)) : '?' ?>
            </div>
            <a href="<?= BASE_URL ?>/forum_create.php<?= $community_id ? '?community_id=' . $community_id : '' ?>" 
               class="reddit-create-input text-decoration-none text-muted d-flex align-items-center">
                <span>Create Post, share screenshot or meme...</span>
            </a>
            <a href="<?= BASE_URL ?>/forum_create.php<?= $community_id ? '?community_id=' . $community_id : '' ?>" 
               class="btn btn-accent rounded-pill px-3">
                <i class="bi bi-plus-lg me-1"></i>Ask
            </a>
        </div>

        <div class="forum-list">
            <?php if (empty($posts)): ?>
                <div class="text-center p-5 border rounded" style="background: var(--bg-card); border-color: var(--border) !important;">
                    <i class="bi bi-chat-dots text-muted" style="font-size: 4rem; opacity: 0.5;"></i>
                    <h4 class="mt-3 text-white">No discussions yet</h4>
                    <p class="text-muted">Be the first to start a conversation in the community!</p>
                </div>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                <div class="reddit-post-card">
                    <div class="reddit-post-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="width: 24px; height: 24px; font-size: 0.55rem; font-weight: 700; background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">
                                pcb/
                            </div>
                            <span class="fw-bold forum-bold-text">
                                <?php if (!empty($post['community_name'])): ?>
                                    <a href="?community_id=<?= $post['community_id'] ?>" class="forum-link-text">pcb/<?= sanitise($post['community_name']) ?></a>
                                <?php else: ?>
                                    <a href="forum.php" class="forum-link-text">pcb/PCBuilderBD</a>
                                <?php endif; ?>
                            </span>
                            <span>•</span>
                            <span>Posted by <span class="text-primary-emphasis">u/<?= sanitise($post['user_name']) ?></span></span>
                            <span>•</span>
                            <span><?= date('M j, Y', strtotime($post['created_at'])) ?></span>
                        </div>
                    </div>

                    <a href="<?= BASE_URL ?>/forum_post.php?id=<?= $post['post_id'] ?>" class="text-decoration-none">
                        <h2 class="reddit-post-title"><?= sanitise($post['title']) ?></h2>
                        <?php if (!empty($post['content'])): ?>
                            <?php 
                                $snippet = strip_tags($post['content']);
                                if(strlen($snippet) > 200) $snippet = substr($snippet, 0, 200) . '...';
                            ?>
                            <p class="reddit-post-content"><?= sanitise($snippet) ?></p>
                        <?php endif; ?>
                    </a>

                    <?php if (!empty($post['image_path'])): ?>
                    <div class="reddit-post-media">
                        <img src="<?= BASE_URL ?>/<?= sanitise($post['image_path']) ?>" alt="Post image">
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($post['tags'])): ?>
                    <div class="mb-3 d-flex gap-2 flex-wrap">
                        <?php 
                            $tags = explode(',', $post['tags']);
                            foreach ($tags as $t): 
                        ?>
                            <span class="tag-badge"><?= sanitise($t) ?></span>
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

                        <a href="<?= BASE_URL ?>/forum_post.php?id=<?= $post['post_id'] ?>" class="action-pill">
                            <i class="bi bi-chat-left-text"></i>
                            <span><?= (int)$post['comment_count'] ?> Comments</span>
                        </a>

                        <a href="#" class="action-pill share-btn" data-url="<?= BASE_URL ?>/forum_post.php?id=<?= $post['post_id'] ?>">
                            <i class="bi bi-share"></i>
                            <span>Share</span>
                        </a>

                        <?php if (is_logged_in() && (is_moderator() || (int)$post['user_id'] === (int)$user_id)): ?>
                        <form method="POST" action="<?= BASE_URL ?>/api/delete_forum_item.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post? This cannot be undone.');">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="type" value="post">
                            <input type="hidden" name="id" value="<?= (int)$post['post_id'] ?>">
                            <button type="submit" class="action-pill text-danger" style="background: rgba(220, 53, 69, 0.1); border-color: rgba(220, 53, 69, 0.2); cursor:pointer;">
                                <i class="bi bi-trash"></i>
                                <span>Delete</span>
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="mt-4">
            <?php 
                $comm_param = $community_id ? '&community_id=' . $community_id : '';
                render_pagination($pag, BASE_URL . '/forum.php?tab=' . urlencode($tab) . $comm_param . '&'); 
            ?>
        </div>
    </div>

    <div class="reddit-right-sidebar">
        <div class="reddit-right-card">
            <div class="reddit-right-card-title">Popular Communities</div>
            <div class="d-flex flex-column gap-3">
                <?php if (empty($sidebar_communities)): ?>
                    <div class="text-muted small">No communities created yet.</div>
                <?php else: ?>
                    <?php foreach ($sidebar_communities as $comm): ?>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <div class="avatar-circle" style="width: 32px; height: 32px; font-size: 0.65rem; font-weight: 700; background: linear-gradient(135deg, var(--accent) 0%, #3b82f6 100%);">pcb/</div>
                            <div>
                                <div class="fw-bold forum-bold-text small">
                                    <a href="?community_id=<?= $comm['community_id'] ?>" class="forum-link-text text-decoration-none">
                                        pcb/<?= sanitise($comm['name']) ?>
                                    </a>
                                </div>
                                <div class="text-muted" style="font-size: 0.75rem;" id="member-count-<?= $comm['community_id'] ?>">
                                    <?= number_format($comm['member_count']) ?> members
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-join-toggle btn-join-action <?= $comm['is_joined'] ? 'joined' : 'not-joined' ?>" 
                                data-id="<?= $comm['community_id'] ?>">
                            <?= $comm['is_joined'] ? 'Joined' : 'Join' ?>
                        </button>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if (is_logged_in()): ?>
            <button class="btn btn-accent w-100 rounded-pill mt-3 py-2 fw-bold" 
                    data-bs-toggle="modal" data-bs-target="#createCommunityModal" 
                    style="font-size: 0.85rem;">
                <i class="bi bi-plus-lg me-1"></i>Create Community
            </button>
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

        <div class="text-muted small px-2" style="font-size: 0.75rem; line-height: 1.5;">
            PCBuilder Inc. © 2026. All rights reserved. PC Builder BD Community.
        </div>
    </div>
</div>

<div class="modal fade" id="createCommunityModal" tabindex="-1" aria-labelledby="createCommunityModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-dark">
            <div class="modal-header modal-header-dark">
                <h5 class="modal-title" id="createCommunityModalLabel"><i class="bi bi-people-fill me-2 text-accent"></i>Create a New Community</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-none" id="modal-error-alert"></div>
                <form id="create-community-form">
                    <div class="mb-3">
                        <label for="community-name-input" class="form-label fw-bold">Community Name</label>
                        <div class="input-group">
                            <span class="input-group-text" style="background-color: var(--bg-input); border-color: var(--border); color: var(--text-primary);">pcb/</span>
                            <input type="text" class="form-control" id="community-name-input" required 
                                   placeholder="e.g. WaterCoolingBD" style="background-color: var(--bg-input); border-color: var(--border); color: var(--text-primary);">
                        </div>
                        <div class="form-text text-muted" style="font-size: 0.8rem;">3-30 letters/numbers only. No spaces.</div>
                    </div>
                    <div class="mb-3">
                        <label for="community-desc-input" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="community-desc-input" rows="3" 
                                  placeholder="Describe what this community is about..." style="background-color: var(--bg-input); border-color: var(--border); color: var(--text-primary);"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer modal-footer-dark">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal" style="border-color: var(--border); color: var(--text-primary);">Cancel</button>
                <button type="button" class="btn btn-accent rounded-pill px-4" id="btn-submit-community">Create Community</button>
            </div>
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

    document.querySelectorAll('.btn-join-action').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (!window.IS_LOGGED_IN) {
                alert("You must be logged in to join communities.");
                return;
            }
            const communityId = btn.dataset.id;
            
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
                    document.querySelectorAll(`.btn-join-action[data-id="${communityId}"]`).forEach(actionBtn => {
                        if (data.joined) {
                            actionBtn.classList.remove('not-joined');
                            actionBtn.classList.add('joined');
                            actionBtn.innerText = 'Joined';
                        } else {
                            actionBtn.classList.remove('joined');
                            actionBtn.classList.add('not-joined');
                            actionBtn.innerText = 'Join';
                        }
                    });
                    
                    const sidebarCountEl = document.getElementById(`member-count-${communityId}`);
                    if (sidebarCountEl) {
                        sidebarCountEl.innerText = `${data.member_count.toLocaleString()} members`;
                    }
                    
                    const bannerCountEl = document.getElementById('banner-members-count');
                    if (bannerCountEl && btn.closest('.community-banner')) {
                        bannerCountEl.innerText = data.member_count.toLocaleString();
                    }
                } else {
                    alert(data.error || "An error occurred.");
                }
            } catch (err) {
                console.error(err);
            }
        });
    });

    const btnSubmitCommunity = document.getElementById('btn-submit-community');
    const modalErrorAlert = document.getElementById('modal-error-alert');
    
    if (btnSubmitCommunity) {
        btnSubmitCommunity.addEventListener('click', async () => {
            const nameInput = document.getElementById('community-name-input').value.trim();
            const descInput = document.getElementById('community-desc-input').value.trim();
            
            if (!nameInput) {
                modalErrorAlert.innerText = 'Community name is required.';
                modalErrorAlert.classList.remove('d-none');
                return;
            }
            
            try {
                const fd = new FormData();
                fd.append('name', nameInput);
                fd.append('description', descInput);
                fd.append('csrf_token', window.CSRF_TOKEN);
                
                const res = await fetch(`${window.BASE_URL}/api/create_community.php`, {
                    method: 'POST',
                    body: fd
                });
                
                const data = await res.json();
                if (data.success) {
                    window.location.href = `${window.BASE_URL}/forum.php?community_id=${data.community_id}`;
                } else {
                    modalErrorAlert.innerText = data.error || "Failed to create community.";
                    modalErrorAlert.classList.remove('d-none');
                }
            } catch (err) {
                console.error(err);
                modalErrorAlert.innerText = "A network error occurred.";
                modalErrorAlert.classList.remove('d-none');
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
        console.log('[DELETE POST] id=' + postId + ' status=' + res.status + ' response=' + text);

        let data;
        try { data = JSON.parse(text); } catch(e) {
            alert('Server error: ' + text.substring(0, 200));
            btn.disabled = false; btn.innerHTML = origHTML; return;
        }

        if (data.success) {
            const card = btn.closest('.reddit-post-card');
            if (card) { card.style.opacity = '0'; setTimeout(() => card.remove(), 300); }
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
