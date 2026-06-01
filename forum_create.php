<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

require_auth();

$error = '';
$title = '';
$content = '';
$selected_community_id = (int)input('community_id', 0);

if (is_post()) {
    verify_csrf();
    $title   = trim(input('title', ''));
    $content = trim(input('content', ''));
    $tags_raw = input('tags', []);
    $tags_input = is_array($tags_raw) ? implode(',', $tags_raw) : trim((string)$tags_raw);
    $comm_id = input('community_id', '');
    $comm_id_val = $comm_id !== '' ? (int)$comm_id : null;

    $is_announcement = input('is_announcement', '') === '1' && is_moderator();
    if ($is_announcement) {
        $tags_list = array_filter(array_map('trim', explode(',', $tags_input)));
        if (!in_array('announcement', $tags_list)) {
            $tags_list[] = 'announcement';
        }
        $tags_input = implode(',', $tags_list);
    }
    
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => 'Image exceeds server upload limit (max 8MB).',
                UPLOAD_ERR_FORM_SIZE  => 'Image exceeds form size limit.',
                UPLOAD_ERR_PARTIAL    => 'Image upload was incomplete. Please try again.',
                UPLOAD_ERR_NO_TMP_DIR => 'Server temporary directory is missing.',
                UPLOAD_ERR_CANT_WRITE => 'Server cannot write the uploaded file.',
                UPLOAD_ERR_EXTENSION  => 'A server extension blocked the upload.',
            ];
            $error = $upload_errors[$_FILES['image']['error']] ?? 'Unknown upload error.';
        } else {
            $fileTmpPath   = $_FILES['image']['tmp_name'];
            $fileName      = $_FILES['image']['name'];
            $fileSize      = $_FILES['image']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $allowedExt    = ['jpg', 'gif', 'png', 'jpeg', 'webp'];
            if (!in_array($fileExtension, $allowedExt)) {
                $error = 'Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
            } elseif ($fileSize > 8000000) {
                $error = 'Image size exceeds 8MB limit.';
            } else {
                $uploadFileDir = __DIR__ . '/uploads/forum/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                $newFileName = md5(uniqid('', true) . $fileName) . '.' . $fileExtension;
                $dest_path   = $uploadFileDir . $newFileName;
                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $image_path = 'uploads/forum/' . $newFileName;
                } else {
                    $error = 'Failed to save image. Check server write permissions.';
                }
            }
        }
    }

    if (strlen($title) < 5 || strlen($title) > 100) {
        $error = 'Title must be between 5 and 100 characters.';
    } elseif (strlen($content) < 10) {
        $error = 'Post content is too short.';
    } elseif (!$error) {
        $user_id = get_auth_user()['id'];
        db_exec('INSERT INTO post (user_id, title, content, image_path, community_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())', [
            $user_id, $title, $content, $image_path, $comm_id_val
        ]);
        $post_id = db_row('SELECT LAST_INSERT_ID() AS id')['id'];
        
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

$all_communities = db_query("SELECT community_id, name FROM community ORDER BY name ASC");

$page_title = 'Create New Post';
include __DIR__ . '/templates/header.php';
?>
<style>
body {
    background-color: #0b1416 !important;
    color: #eaedef !important;
}
.navbar {
    background-color: #0b1416 !important;
    border-bottom: 1px solid #223035 !important;
}
#main-nav .navbar-brand, #main-nav .nav-link {
    color: #eaedef !important;
}

.reddit-form-card {
    background-color: #0f1a1c;
    border: 1px solid #223035;
    border-radius: 16px;
    padding: 30px;
    margin-top: 20px;
}
.reddit-form-card label {
    color: #eaedef;
    font-weight: 600;
    margin-bottom: 8px;
}
.reddit-form-card .form-control, 
.reddit-form-card .form-select {
    background-color: #1a282d;
    border: 1px solid #223035;
    color: #eaedef;
    border-radius: 8px;
}
.reddit-form-card .form-control:focus, 
.reddit-form-card .form-select:focus {
    background-color: #22353c;
    border-color: #ff4500;
    color: #eaedef;
    box-shadow: none;
}
.reddit-form-card .form-text {
    color: #82959b;
}

#content.dragover {
    border: 2px dashed #ff4500 !important;
    background-color: #22353c !important;
}

.preview-box {
    position: relative;
    display: inline-block;
    max-width: 100%;
    margin-top: 15px;
    padding: 8px;
    background: #1a282d;
    border: 1px solid #223035;
    border-radius: 12px;
}
.preview-box img {
    max-height: 250px;
    border-radius: 8px;
    border: 1px solid #223035;
}
.preview-box .btn-close-preview {
    position: absolute;
    top: -10px;
    right: -10px;
    background: #ff4500;
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.btn-accent-orange {
    background-color: #ff4500 !important;
    border: none !important;
    color: #fff !important;
    font-weight: 600;
    border-radius: 999px;
    padding: 8px 24px;
    transition: opacity 0.15s;
}
.btn-accent-orange:hover {
    opacity: 0.9;
}

.tag-chip-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.tag-chip {
    background: #1a282d;
    border: 1px solid #2d4550;
    color: #82959b;
    border-radius: 999px;
    padding: 5px 14px;
    font-size: 0.82rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.18s ease;
    outline: none;
}
.tag-chip:hover {
    border-color: #ff4500;
    color: #fff;
}
.tag-chip.selected {
    background: #ff4500;
    border-color: #ff4500;
    color: #fff;
    font-weight: 600;
}
</style>

<div class="container-xl py-4" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4 gap-3">
        <a href="<?= BASE_URL ?>/forum.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="bi bi-arrow-left me-1"></i>Back to discussions</a>
        <h1 class="section-title mb-0 flex-grow-1" style="font-size: 1.8rem; color: #eaedef;"><i class="bi bi-pencil-square me-2 text-accent"></i>Create New Post</h1>
    </div>

    <div class="reddit-form-card shadow-sm">
        <?php if ($error): ?>
            <div class="alert alert-danger" style="background-color: #f87171; color: #000; border: none;"><i class="bi bi-exclamation-triangle me-2"></i><?= sanitise($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="post-form">
            <?php csrf_field(); ?>
            
            <div class="mb-3">
                <label for="community_id" class="form-label">Choose a Community</label>
                <select class="form-select" id="community_id" name="community_id">
                    <option value="">pcb/PCBuilderBD (General Forum)</option>
                    <?php foreach ($all_communities as $comm): ?>
                        <option value="<?= $comm['community_id'] ?>" <?= $selected_community_id === (int)$comm['community_id'] ? 'selected' : '' ?>>
                            pcb/<?= sanitise($comm['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Choose where you want to post your build update or hardware discussion.</div>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Post Title</label>
                <input type="text" class="form-control" id="title" name="title" required minlength="5" maxlength="100" 
                       placeholder="Title of your post..." value="<?= sanitise($title) ?>">
                <div class="form-text">Keep it clear and descriptive (5-100 characters).</div>
            </div>

            <input type="file" class="d-none" id="image" name="image" accept="image/*">

            <div class="mb-4">
                <label for="content" class="form-label">Content</label>
                <textarea class="form-control" id="content" name="content" rows="12" required minlength="10" 
                          placeholder="Share your thoughts, ask a question, or post a build... Drag and drop or paste images directly in this box!"><?= sanitise($content) ?></textarea>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i> You can drag &amp; drop an image here or copy-paste (Ctrl+V) directly into the content box to attach it! (Max size: 8MB)
                </div>
                
                <div id="preview-container" class="d-none">
                    <div class="preview-box">
                        <div class="text-muted small mb-2"><i class="bi bi-paperclip me-1"></i>Attached Image:</div>
                        <img id="image-preview" src="#" alt="Preview">
                        <button type="button" class="btn-close-preview" id="btn-clear-preview"><i class="bi bi-x"></i></button>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Tags <span class="text-muted fw-normal">(optional)</span></label>
                <div class="tag-chip-grid mb-2" id="tag-chip-grid">
                    <?php
                    $preset_tags = [
                        'build'       => 'Build / Showcase',
                        'gpu'         => 'Graphics Cards',
                        'cpu'         => 'Processors',
                        'budget'      => 'Budget Advice',
                        'support'     => 'Tech Support',
                        'news'        => 'Hardware News',
                        'setup'       => 'Setup & Peripherals',
                        'overclocking'=> 'Overclocking',
                        'ram'         => 'Memory / RAM',
                        'storage'     => 'Storage / SSD',
                        'cooling'     => 'Cooling',
                        'monitor'     => 'Monitor / Display',
                    ];
                    foreach ($preset_tags as $val => $label): ?>
                    <button type="button" class="tag-chip" data-value="<?= $val ?>"><?= $label ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="tags" id="tags-hidden" value="">
                <div class="input-group mt-2" style="max-width: 340px;">
                    <input type="text" class="form-control" id="custom-tag-input" placeholder="Add a custom tag..." maxlength="30" style="background:#1a282d;border-color:#223035;color:#eaedef;">
                    <button type="button" class="btn" style="background:#223035;color:#eaedef;border-color:#223035;" id="add-custom-tag-btn"><i class="bi bi-plus-lg"></i> Add</button>
                </div>
                <div class="form-text">Click tags to select/deselect. You can also add a custom tag above.</div>
            </div>

            <?php if (is_moderator()): ?>
            <div class="mb-4">
                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background: linear-gradient(135deg, rgba(255,165,0,0.1), rgba(255,69,0,0.08)); border: 1px solid rgba(255,165,0,0.25);">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="bi bi-megaphone-fill" style="color: #ffa500; font-size: 1.1rem;"></i>
                            <span class="fw-bold" style="color: #ffa500;">Post as Announcement</span>
                            <span class="badge ms-1" style="background: rgba(255,165,0,0.2); color: #ffa500; border: 1px solid rgba(255,165,0,0.4); font-size: 0.7rem;">ADMIN / MOD</span>
                        </div>
                        <div class="text-muted small">Announcements appear in the dedicated Announcements tab and are pinned for visibility.</div>
                    </div>
                    <div class="form-check form-switch ms-2">
                        <input class="form-check-input" type="checkbox" role="switch" id="is_announcement" name="is_announcement" value="1" style="width: 3rem; height: 1.5rem; cursor: pointer;">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end gap-2">
                <a href="<?= BASE_URL ?>/forum.php" class="btn btn-outline-secondary rounded-pill px-4" style="border-color: #223035; color: #eaedef;">Cancel</a>
                <button type="submit" class="btn btn-accent-orange"><i class="bi bi-send me-2"></i>Publish Post</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('image');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');
    const btnClearPreview = document.getElementById('btn-clear-preview');
    const contentTextarea = document.getElementById('content');
    const tagsHidden = document.getElementById('tags-hidden');
    const customTagInput = document.getElementById('custom-tag-input');
    const addCustomTagBtn = document.getElementById('add-custom-tag-btn');
    const chipGrid = document.getElementById('tag-chip-grid');

    let selectedTags = new Set();

    function syncTagsHidden() {
        tagsHidden.value = Array.from(selectedTags).join(',');
    }

    chipGrid.addEventListener('click', (e) => {
        const chip = e.target.closest('.tag-chip');
        if (!chip) return;
        const val = chip.dataset.value;
        if (selectedTags.has(val)) {
            selectedTags.delete(val);
            chip.classList.remove('selected');
        } else {
            selectedTags.add(val);
            chip.classList.add('selected');
        }
        syncTagsHidden();
    });

    function addCustomTag() {
        const raw = customTagInput.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '');
        if (!raw || raw.length < 2) return;
        if (selectedTags.has(raw)) { customTagInput.value = ''; return; }
        const existing = chipGrid.querySelector(`[data-value="${raw}"]`);
        if (existing) {
            existing.click();
        } else {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'tag-chip selected';
            btn.dataset.value = raw;
            btn.textContent = raw;
            chipGrid.appendChild(btn);
            selectedTags.add(raw);
            syncTagsHidden();
        }
        customTagInput.value = '';
    }

    addCustomTagBtn.addEventListener('click', addCustomTag);
    customTagInput.addEventListener('keydown', (e) => { if (e.key === 'Enter') { e.preventDefault(); addCustomTag(); } });

    function showPreview(file) {
        if (!file || !file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.src = e.target.result;
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }

    function clearPreview() {
        fileInput.value = '';
        imagePreview.src = '#';
        previewContainer.classList.add('d-none');
    }

    contentTextarea.addEventListener('dragover', (e) => {
        e.preventDefault();
        contentTextarea.classList.add('dragover');
    });
    contentTextarea.addEventListener('dragleave', () => contentTextarea.classList.remove('dragover'));
    contentTextarea.addEventListener('drop', (e) => {
        e.preventDefault();
        contentTextarea.classList.remove('dragover');
        if (e.dataTransfer.files.length > 0) {
            const file = e.dataTransfer.files[0];
            if (file.type.startsWith('image/')) {
                fileInput.files = e.dataTransfer.files;
                showPreview(file);
                toast('Image dropped and attached!');
            }
        }
    });
    contentTextarea.addEventListener('paste', (e) => {
        const items = (e.clipboardData || window.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;
                showPreview(file);
                toast('Image pasted from clipboard!');
                break;
            }
        }
    });
    btnClearPreview.addEventListener('click', (e) => { e.stopPropagation(); clearPreview(); });

    function toast(msg) {
        const el = document.createElement('div');
        el.className = 'alert mt-2 py-1 px-3';
        el.style.cssText = 'font-size:.85rem;background:#10b981;color:#fff;border:none;border-radius:8px;';
        el.innerHTML = `<i class="bi bi-check-circle-fill me-2"></i>${msg}`;
        contentTextarea.parentNode.insertBefore(el, contentTextarea.nextSibling);
        setTimeout(() => el.remove(), 3000);
    }
});
</script>
<?php include __DIR__ . '/templates/footer.php'; ?>

