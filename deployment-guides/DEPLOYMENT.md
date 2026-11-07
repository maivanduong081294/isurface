# GitHub → cPanel deployment

Tài liệu này mô tả cách deploy riêng nội dung trong thư mục `wordpress/` lên cPanel bằng GitHub Actions và SSH.

## 1. Tạo SSH key deploy

Trên máy local, mở PowerShell:

```powershell
New-Item -ItemType Directory -Force "$env:USERPROFILE\.ssh"

ssh-keygen -t ed25519 `
  -f "$env:USERPROFILE\.ssh\isurface_deploy" `
  -C "github-actions-isurface"
```

Khi hỏi passphrase, có thể nhấn Enter hai lần để dùng key không có passphrase cho GitHub Actions.

Hai file được tạo:

```text
isurface_deploy       # private key - không chia sẻ, không commit
isurface_deploy.pub   # public key - đưa lên cPanel
```

## 2. Đưa public key lên cPanel

Vào:

```text
cPanel → SSH Access → Manage SSH Keys → Import Key
```

Điền tên `isurface_deploy`, dán nội dung `isurface_deploy.pub` vào ô **Public Key**, để trống **Private Key**, bấm **Import**.

Sau đó bấm **Manage → Authorize** cho key vừa tạo.

Không upload private key lên cPanel và không gửi private key vào chat.

## NOTE: lấy SSH port từ hosting

Không đoán SSH port từ URL cPanel. Port `2083` là cổng giao diện cPanel HTTPS, không phải SSH.

Để lấy đúng SSH port:

1. Vào cPanel → **Git™ Version Control**.
2. Tạo hoặc mở repository.
3. Bấm **Manage**.
4. Xem trường **Clone URL**.

Ví dụ:

```text
ssh://yaithrka@isurface.vn:2210/home/yaithrka/isurface
```

Số sau dấu `:` là SSH port:

```text
Host: isurface.vn
Port: 2210
User: yaithrka
```

## 3. Kiểm tra SSH

```powershell
ssh -i "$env:USERPROFILE\.ssh\isurface_deploy" `
  -p 2210 `
  yaithrka@isurface.vn
```

Nếu Windows báo `UNPROTECTED PRIVATE KEY FILE`, khóa quyền file:

```powershell
$key = "$env:USERPROFILE\.ssh\isurface_deploy"
icacls $key /inheritance:r
icacls $key /grant:r "$env:USERNAME:F"
```

## 4. Tạo GitHub Repository Secrets

Vào:

```text
Repository → Settings → Secrets and variables → Actions
```

Tạo các **Repository secrets** sau:

| Name | Value |
|---|---|
| `DEPLOY_HOST` | `isurface.vn` |
| `DEPLOY_PORT` | `2210` |
| `DEPLOY_USER` | `yaithrka` |
| `DEPLOY_PATH` | `/home/yaithrka/public_html` |
| `DEPLOY_SSH_KEY` | toàn bộ nội dung private key `isurface_deploy` |

`DEPLOY_SSH_KEY` phải bao gồm cả dòng `BEGIN` và `END`. Không ghi secret vào source.

## 5. Workflow deploy

Workflow nằm tại:

```text
.github/workflows/deploy.yml
```

Workflow chạy khi push vào branch `main` và chỉ đồng bộ:

```text
wordpress/ → /home/yaithrka/public_html/
```

Không deploy các file ở root project như `docker-compose.yml`, `.env.example` hoặc `.cpanel.yml`.

Các nội dung được giữ riêng trên hosting:

```text
wordpress/wp-config.php
wordpress/wp-content/uploads/
wordpress/wp-content/cache/
```

## 6. Test deploy

```powershell
git add wordpress/
git commit -m "test: deploy WordPress source"
git push origin main
```

Theo dõi tại:

```text
GitHub → Actions → Deploy WordPress to cPanel
```

Các bước cần màu xanh:

```text
Checkout source
Configure SSH
Deploy WordPress files
```

Sau đó kiểm tra website và thời gian sửa file trong:

```text
cPanel → File Manager → /home/yaithrka/public_html/
```

## 7. Bảo mật

- Không commit private key, `.env` hoặc `wp-config.php`.
- Nếu private key bị gửi nhầm, xóa/revoke public key tương ứng trên cPanel và tạo key mới.
- Không dùng mật khẩu database local `123456` trên hosting.
- Không dùng `2083` làm SSH port; đó là cPanel HTTPS.

