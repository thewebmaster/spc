# Southern Phone

## Southern Phone Programming Test

### Environment
- OS: Ubuntu 18.04.2 LTS
- PHP Version: 7.2.19
- DB: mariadb:10.1
- PHP Framework: Codeigniter 3.1.11

### How to run from your localhost:
1. Install docker if you haven't installed already.
2. Login to Christian Turno's docker repository using the provided credentials.
```bash
docker login docker.turno.co.nz:2053
```
3. Run the following docker command:
```bash
docker pull docker.turno.co.nz:2053/southern-phone:latest-prod \
&& docker run -d \
--name southern-phone \
--rm \
--env "BASE_URL=http://localhost:8080/" \
--env "DB_HOST=[WRITE_YOUR_DB_HOST_HERE]" \
--env "DB_USERNAME=[WRITE_YOUR_DB_USERNAME_HERE]" \
--env "DB_PASSWORD=[WRITE_YOUR_DB_PASSWORD_HERE]" \
--env "DB_NAME=[WRITE_YOUR_DB_NAME_HERE]" \
-p 8080:80 \
docker.turno.co.nz:2053/southern-phone:latest-prod
```
4. Open your http://localhost:8080/ on your browser.

