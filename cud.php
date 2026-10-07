<?php


class Cud {


    public $userId;
    public $rootPath;
    public $foldersFile;
    public $filesFile;
    public $folders;
    public $files;
    public $currentPath;

    function __construct() {
        $this->userId      = $_SESSION['userId'];
        $this->rootPath    = 'storage/' . $this->userId;
        $this->foldersFile = $this->userFoldersPath();
        $this->filesFile   = $this->userFilesPath();

        $this->ensureUserRootFolder();

        $this->folders     = $this->loadJsonFile($this->foldersFile);
        $this->files       = $this->loadJsonFile($this->filesFile);

        $this->currentPath = $this->CurrentFolder();
    }


    function requireLogin(): void
    {
        if (!isset($this->userId)) {
            header("Location: login.php");
            exit;
        }
    }

    function isLoggedin(): void
    {
        if (isset($this->userId)) {
            header("Location: home.php");
            exit;
        }
    }

    function logout(): void
    {
        if (isset($_POST["logout-btn"])) {
            session_unset();
            session_destroy();
            header('Location: login.php');
            exit;
        }
    }

    function loadJsonFile(string $path): array
    {
        if (!file_exists($path)) {
            file_put_contents($path, '[]');
        }
        return json_decode(file_get_contents($path), true) ?: [];
    }

    function saveJsonFile(string $path, array $data): void
    {
        file_put_contents($path, json_encode(array_values($data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    function ensureUserRootFolder(): void
    {
        if (!is_dir(__DIR__ . '/' . $this->rootPath)) {
            mkdir(__DIR__ . '/' . $this->rootPath, 0755, true);
        }
    }


    function userFoldersPath(): string
    {
        return __DIR__ . '/storage/' . $this->userId . '/folders.json';
    }

    function userFilesPath(): string
    {
        return __DIR__ . '/storage/' . $this->userId . '/files.json';
    }

    function CurrentFolder(): string
    {

        $requestedPath = $_GET['folder'] ?? '';

        if ($requestedPath === '') {
            return $this->rootPath;
        }

        
        if (strpos($requestedPath, $this->rootPath) !== 0) {
            return $this->rootPath;
        }

        foreach ($this->folders as $f) {
            if ($f['path'] === $requestedPath) {
                return $f['path'];
            }
        }

        return $this->rootPath;
    }

    function createFolder(): string
    {
        if (!isset($_POST["createFolderBtn"])) {
            return '';
        }

        $folderName = basename(trim($_POST["folderName"]));
        $newPath    = $this->currentPath . '/' . $folderName;

        if ($folderName === '') {
            return "Folder name cannot be empty.";
        }
        if (file_exists(__DIR__ . '/' . $newPath)) {
            return "A folder with this name already exists.";
        }
        if (!mkdir(__DIR__ . '/' . $newPath, 0755)) {
            return "Failed to create folder. Check permissions.";
        }

        $this->folders[] = [
            'name'       => $folderName,
            'parent'     => $this->currentPath,
            'path'       => $newPath,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->saveJsonFile($this->foldersFile, $this->folders);

        header("Location: home.php?folder=" . urlencode($this->currentPath));
        exit;
    }

    function uploadFile(): string
    {
        if (!isset($_POST["uploadFileBtn"])) {
            return '';
        }

        if (!isset($_FILES['uploadedFile']) || $_FILES['uploadedFile']['error'] !== UPLOAD_ERR_OK) {
            return "No file selected or an upload error occurred.";
        }

        $fileName = basename($_FILES['uploadedFile']['name']);
        $ext      = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'pdf'];

        if (!in_array($ext, $allowedExt)) {
            return "Only images and PDF files are allowed.";
        }

        $newPath = $this->currentPath . '/' . $fileName;

        if (file_exists(__DIR__ . '/' . $newPath)) {
            return "A file with this name already exists.";
        }
        if (!move_uploaded_file($_FILES['uploadedFile']['tmp_name'], __DIR__ . '/' . $newPath)) {
            return "Failed to upload file.";
        }

        $this->files[] = [
            'name'       => $fileName,
            'parent'     => $this->currentPath,
            'type'       => $ext,
            'path'       => $newPath,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->saveJsonFile($this->filesFile, $this->files);

        header("Location: home.php?folder=" . urlencode($this->currentPath));
        exit;
    }

    function deleteFile(): void
    {
        if (!isset($_GET['deleteFile'])) {
            return;
        }

        $targetPath = $_GET['deleteFile'];

        foreach ($this->files as $k => $f) {
            if ($f['path'] === $targetPath) {
                @unlink(__DIR__ . '/' . $f['path']);
                unset($this->files[$k]);
                $this->saveJsonFile($this->filesFile, $this->files);
                break;
            }
        }
        header('Location: home.php?folder=' . urlencode($this->currentPath));
        exit;
    }

    function deleteFolderRecursively(string $relativePath): void
    {
        $fullPath = __DIR__ . '/' . $relativePath;

        if (!is_dir($fullPath)) {
            return;
        }

        $entries = scandir($fullPath);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $entryRelative = $relativePath . '/' . $entry;
            $entryFull     = __DIR__ . '/' . $entryRelative;

            if (is_dir($entryFull)) {
                $this->deleteFolderRecursively($entryRelative);
            } else {
                @unlink($entryFull);
                foreach ($this->files as $k => $f) {
                    if ($f['path'] === $entryRelative) {
                        unset($this->files[$k]);
                        break;
                    }
                }
            }
        }

        @rmdir($fullPath);

        foreach ($this->folders as $k => $f) {
            if ($f['path'] === $relativePath) {
                unset($this->folders[$k]);
                break;
            }
        }
    }

    function deleteFolder(): void
    {
        if (!isset($_GET['deleteFolder'])) {
            return;
        }

        $targetPath = $_GET['deleteFolder'];

        $this->deleteFolderRecursively($targetPath);

        $this->saveJsonFile($this->foldersFile, $this->folders);
        $this->saveJsonFile($this->filesFile, $this->files);

        header('Location: home.php?folder=' . urlencode($this->currentPath));
        exit;
    }

    function buildRows(): array
    {
        $rows = [];
        foreach ($this->folders as $f) {
            if ($f['parent'] === $this->currentPath) {
                $rows[] = $f + ['kind' => 'folder'];
            }
        }
        foreach ($this->files as $f) {
            if ($f['parent'] === $this->currentPath) {
                $rows[] = $f + ['kind' => 'file'];
            }
        }
        return $rows;
    }
}