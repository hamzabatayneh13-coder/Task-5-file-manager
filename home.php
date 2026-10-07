<?php
session_start();

require 'cud.php';

$cud_functions = new Cud();

$cud_functions->requireLogin();
$cud_functions->logout();

$folderError = $cud_functions->createFolder();
$uploadError = $cud_functions->uploadFile();

$cud_functions->deleteFile();
$cud_functions->deleteFolder();

$rows = $cud_functions->buildRows();

$currentPath = $cud_functions->currentPath;
$rootPath    = $cud_functions->rootPath;

$imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'pdf'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="home-style.css">
</head>
<body>
    <div class="header">
        <h1>File Management System</h1>
        <div class="header-logout">
            <form action="" method="post">
                <button class="logout-btn-btn" name="logout-btn" type="submit">
                    <i class="bi bi-box-arrow-right">
                        <span>Log Out</span>
                    </i>
                </button>
            </form>
            <p>
                <i class="bi bi-person">
                    <span><?php echo htmlspecialchars($_SESSION['firstname']); ?></span>
                </i>
            </p>
        </div>
    </div>

    <div class="create-upload">
        <h1>
            <img src="icons/folder-icon.png" alt="">
            <span>File Manager</span>
        </h1>

        <div class="buttons">
            <a href="#createFolderModal"><button type="button" class="create-btn">Create Folder</button></a>
            <a href="#uploadFileModal"><button type="button" class="upload-btn">Upload File</button></a>
        </div>
    </div>


    <div class="breadcrumbs">
        <a href="home.php">Home</a>
        <?php
        if ($currentPath !== $rootPath) {
            $relative = trim(substr($currentPath, strlen($rootPath)), '/');
            $parts    = explode('/', $relative);
            $pathSoFar = $rootPath;
            foreach ($parts as $part) {
                $pathSoFar .= '/' . $part;
                echo ' / <a href="home.php?folder=' . urlencode($pathSoFar) . '">' . htmlspecialchars($part) . '</a>';
            }
        }
        ?>
    </div>

    <div class="my-folders-files">
        <table class="file-table">
            <thead>
                <tr>
                    <th>Title/Name</th>
                    <th>File Type</th>
                    <th>Date Added</th>
                    <th>Manage</th>
                    <th><input type="checkbox"></th>
                </tr>
            </thead>
            <tbody>

                <?php
                $viewModals = '';

                foreach ($rows as $i => $row) {
                    $isFolder  = $row['kind'] === 'folder';
                    $ext       = $isFolder ? '' : strtolower($row['type']);
                    $isImage   = !$isFolder && in_array($ext, $imageExts);
                    $dateAdded = date('m/d/Y  g:i a', strtotime($row['created_at']));

                    if ($isFolder) {
                        $typeLabel = 'Folder';
                        $icon      = '📁';
                        $link      = 'home.php?folder=' . urlencode($row['path']);
                        $delete    = 'home.php?deleteFolder=' . urlencode($row['path']) . '&folder=' . urlencode($currentPath);
                    } else {
                        $typeLabel = ($isImage ? 'Image/' : 'File/') . $ext;
                        $icon      = '📄';
                        $link      = $row['path'];
                        $delete    = 'home.php?deleteFile=' . urlencode($row['path']) . '&folder=' . urlencode($currentPath);
                    }

                    $modalId = 'viewModal' . $i;

                    ob_start(); ?>

                    <div id="<?php echo $modalId; ?>" class="modal-overlay">
                        <div class="modal-box">
                            <a href="#" class="modal-close">&times;</a>
                            <h3><?php echo $isFolder ? 'Folder Details' : 'File Details'; ?></h3>

                            <div class="details-list">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                                <p><strong>Type:</strong> <?php echo htmlspecialchars($typeLabel); ?></p>
                                <p><strong>Date Added:</strong> <?php echo $dateAdded; ?></p>
                                <p><strong>Path:</strong> <?php echo htmlspecialchars($row['path']); ?></p>
                            </div>


                        </div>
                    </div>

                    <?php
                    $viewModals .= ob_get_clean();
                    ?>

                    <tr>
                        <td>
                            <?php echo $icon; ?>
                            <a href="<?php echo htmlspecialchars($link); ?>"><?php echo htmlspecialchars($row['name']); ?></a>
                            <br>
                            <span class="sub-name"><?php echo htmlspecialchars($row['name']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($typeLabel); ?></td>
                        <td><?php echo $dateAdded; ?></td>
                        <td class="manage-icons">
                            <a href="#<?php echo $modalId; ?>" class="icon-view"><i class="bi bi-eye-fill"></i></a>
                            <a href="<?php echo htmlspecialchars($delete); ?>" class="icon-delete"><i class="bi bi-trash"></i></a>
                        </td>
                        <td><input type="checkbox" name="selected[]" value="<?php echo htmlspecialchars($row['path']); ?>"></td>
                    </tr>

                    <?php
                }
                ?>
            </tbody>
        </table>

        <?php echo $viewModals; ?>
    </div>

    <div id="createFolderModal" class="modal-overlay">
        <div class="modal-box">
            <a href="#" class="modal-close">&times;</a>
            <h3>Create Folder</h3>

            <?php if ($folderError): ?>
                <p class="error"><?php echo htmlspecialchars($folderError); ?></p>
            <?php endif; ?>

            <form action="home.php?folder=<?php echo urlencode($currentPath); ?>" method="post">
                <input type="text" name="folderName" placeholder="" required>
                <div class="modal-actions">
                    <button type="submit" name="createFolderBtn">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div id="uploadFileModal" class="modal-overlay">
        <div class="modal-box">
            <a href="#" class="modal-close">&times;</a>
            <h3>Upload File</h3>

            <?php if ($uploadError): ?>
                <p class="error"><?php echo htmlspecialchars($uploadError); ?></p>
            <?php endif; ?>

            <form action="home.php?folder=<?php echo urlencode($currentPath); ?>" method="post" enctype="multipart/form-data">
                <input type="file" name="uploadedFile" required>
                <div class="modal-actions">
                    <button type="submit" name="uploadFileBtn">Upload</button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <h2>Crafted at training with Sprintive</h2>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>