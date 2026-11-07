# Hướng dẫn Docker PHP → GitHub → cPanel

> Tài liệu mẫu áp dụng cho nhiều project WordPress khác nhau.

## 1. Cài Docker Desktop và WSL2

Trên Windows:

1. Cài **Docker Desktop**.
2. Khi cài, chọn **Use WSL 2 instead of Hyper-V** nếu có lựa chọn.
3. Mở PowerShell và chạy:

```powershell
wsl --set-default-version 2
docker run hello-world
```

Nếu thấy thông báo `Hello from Docker!` là Docker hoạt động.

## 2. Cấu trúc thư mục project

Mở thư mục project bằng VS Code:

```text
C:\Users\Silver\.copilot\chats\b6d1d3d6-0cbb-4185-a087-aac5f1451302
```

Các file/thư mục quan trọng:

```text
docker-compose.yml       # Cấu hình PHP, MySQL, phpMyAdmin
wordpress/                # Source WordPress cần chỉnh sửa và deploy
.github/workflows/deploy.yml
.env                     # Mật khẩu local, không push lên GitHub
```

## 3. Khởi động PHP, MySQL và phpMyAdmin

Mở PowerShell tại thư mục project:

```powershell
docker compose up -d
docker compose ps
```

Mở trên trình duyệt:

```text
WordPress:  http://localhost:8080
phpMyAdmin: http://localhost:8081
```

Thông tin database local:

```text
Database host: db
Database name: wordpress
Database user: wordpress
Database password: xem trong file .env
Root password: xem trong file .env
```

Trong phpMyAdmin, dùng `db` làm **Server**, không dùng `localhost`, vì phpMyAdmin chạy trong container riêng.

## 4. Chỉnh sửa source PHP

Source nằm trong thư mục:

```text
wordpress\
```

Ví dụ theme:

```text
wordpress\wp-content\themes\seooneclick\
```

Mở thư mục này bằng VS Code và chỉnh file PHP trực tiếp. Docker Compose bind mount `./wordpress:/var/www/html`, nên thay đổi trong thư mục `wordpress` sẽ lập tức xuất hiện trong website local.

Sau khi chỉnh sửa, truy cập:

```text
http://localhost:8080
```

Nếu thay đổi cấu hình Docker, chạy lại:

```powershell
docker compose up -d --build
```

Xem log:

```powershell
docker compose logs -f wordpress
docker compose logs -f db
```

Dừng hệ thống:

```powershell
docker compose down
```

Không dùng `docker compose down -v` nếu chưa muốn xóa dữ liệu MySQL.

## 5. Commit và push source lên GitHub

Kiểm tra thay đổi:

```powershell
git status
```

Commit và push:

```powershell
git add wordpress
git commit -m "init"
git push origin main
```

GitHub nhận toàn bộ commit, nhưng workflow dùng `rsync` nên khi deploy chỉ truyền file mới hoặc file đã thay đổi. File bị xóa trong GitHub hiện chưa bị xóa ở hosting vì workflow không dùng `--delete`.

Không commit:

```text
.env
wordpress/wp-config.php
wordpress/wp-content/uploads/
wordpress/wp-content/cache/
```

## 6. Lấy SSH host và port trên cPanel

Port `2083` trên URL cPanel là **port giao diện HTTPS**, không phải port SSH.

Để lấy SSH port:

1. Đăng nhập cPanel.
2. Mở **Tools → Git™ Version Control** hoặc **SSH Access**.
3. Tạo hoặc quản lý một repository Git.
4. Xem dòng **Clone URL**.

Ví dụ:

```text
ssh://SSH_USER@SSH_HOST:SSH_PORT/home/SSH_USER/REPOSITORY
```

Thông tin cần dùng:

```text
SSH host: SSH_HOST
SSH user: SSH_USER
SSH port: SSH_PORT
Deploy path: /home/SSH_USER/public_html
```

Không dùng `2083` làm SSH port nếu Clone URL hiển thị port khác.

## 7. Tạo SSH key riêng cho GitHub Actions

Mở PowerShell trên máy Windows:

```powershell
New-Item -ItemType Directory -Force "$env:USERPROFILE\.ssh"
ssh-keygen -t ed25519 `
  -f "$env:USERPROFILE\.ssh\project_deploy" `
  -C "github-actions-project"
```

Khi được hỏi passphrase, nhấn Enter hai lần để bỏ trống. Workflow hiện tại dùng key không có passphrase.

Hai file được tạo:

```text
%USERPROFILE%\.ssh\project_deploy       # Private key, giữ bí mật
%USERPROFILE%\.ssh\project_deploy.pub   # Public key
```

## 8. Đưa public key vào cPanel

1. cPanel → **SSH Access** → **Manage SSH Keys**.
2. Chọn **Import Key**.
3. Key Name: `project_deploy`.
4. Mở file `project_deploy.pub`, copy toàn bộ nội dung vào **Public Key**.
5. Để trống **Private Key**.
6. Import key.
7. Chọn **Manage → Authorize**.

Chỉ public key được đưa lên cPanel. Không đưa private key lên cPanel, không commit private key vào GitHub.

## 9. Tạo GitHub Repository Secrets

Mở:

```text
https://github.com/USERNAME/REPOSITORY/settings/secrets/actions
```

Chọn **New repository secret** và tạo:

| Name | Value |
|---|---|
| `DEPLOY_SSH_KEY` | Toàn bộ nội dung file `project_deploy` private key |
| `DEPLOY_HOST` | Hostname hoặc IP hosting |
| `DEPLOY_PORT` | SSH port lấy từ cPanel |
| `DEPLOY_USER` | SSH username của hosting |
| `DEPLOY_PATH` | `/home/SSH_USER/public_html` |

Private key phải bao gồm cả dòng BEGIN và END:

```text
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

Không gửi private key trong chat, không chụp màn hình và không đặt trong source code.

## 10. Workflow deploy hoạt động như thế nào

File workflow:

```text
.github/workflows/deploy.yml
```

Mỗi lần push vào branch `main`, GitHub Actions sẽ:

1. Checkout source.
2. Tạo SSH key tạm từ `DEPLOY_SSH_KEY`.
3. Kết nối đến `DEPLOY_HOST:DEPLOY_PORT`.
4. Dùng `rsync` đồng bộ thư mục `wordpress/`.
5. Đưa source vào thư mục được khai báo trong `DEPLOY_PATH`.

Các file runtime được loại trừ:

```text
wp-config.php
wp-content/uploads/
wp-content/cache/
```

## 11. Test commit và deploy

Tạo một thay đổi nhỏ trong file PHP:

```powershell
git add wordpress
git commit -m "test: deploy wordpress source"
git push origin main
```

Xem job tại:

```text
GitHub repository → Actions → Deploy WordPress to cPanel
```

Mở workflow mới nhất. Các bước màu xanh nghĩa là deploy thành công. Sau đó kiểm tra:

```text
https://YOUR_DOMAIN.com
```

Có thể chạy thủ công bằng **Actions → Deploy WordPress to cPanel → Run workflow**.

## 12. Nếu deploy lỗi

Kiểm tra:

1. `DEPLOY_SSH_KEY` là private key mới, không phải file `.pub`.
2. Public key đã được **Authorize** trên cPanel.
3. `DEPLOY_PORT` là SSH port lấy từ cPanel, không mặc định là `2083`.
4. `DEPLOY_PATH` đúng với thư mục public của hosting.
5. `DEPLOY_USER` đúng là SSH username của hosting.
6. Secret được tạo ở **Repository secrets**, không nhầm Environment secrets.
7. Xem chi tiết bước lỗi trong GitHub Actions.

Nếu private key đã từng bị lộ, hãy xóa key cũ trên cPanel, tạo key mới và cập nhật lại `DEPLOY_SSH_KEY`.

## 13. Quy trình sử dụng hằng ngày

```text
1. Mở Docker Desktop
2. docker compose up -d
3. Chỉnh source trong wordpress/
4. Test tại http://localhost:8080
5. git add wordpress
6. git commit -m "mô tả thay đổi"
7. git push origin main
8. Kiểm tra GitHub Actions
9. Kiểm tra domain production của project.
```

Database local và database hosting là hai môi trường riêng. Deploy source PHP không tự động đồng bộ dữ liệu MySQL, bài viết, tài khoản hoặc media uploads.

