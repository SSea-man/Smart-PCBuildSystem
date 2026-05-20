import requests
import re
import time

BASE_URL = "http://localhost:80/myproject/"
REGISTER = BASE_URL + "register.php"

def test_post_myproject_register_php_with_valid_user_data_and_csrf_token():
    session = requests.Session()
    
    # Step 1: GET the register.php page to extract CSRF token from form
    register_page_resp = session.get(REGISTER, timeout=30)
    assert register_page_resp.status_code == 200
    match = re.search(r'name="csrf_token"\s+value="([\w\-]+)"', register_page_resp.text)
    assert match is not None, "CSRF token not found in register page"
    csrf_token = match.group(1)

    # Step 2: Prepare unique user data for registration
    unique_email = f"testuser_{int(time.time())}@pcbuild.com"
    user_data = {
        "name": "Test User",
        "email": unique_email,
        "password": "ValidPass123!",
        "confirm_password": "ValidPass123!",
        "csrf_token": csrf_token
    }

    # Step 3: POST to register.php with valid user data & CSRF token
    register_resp = session.post(REGISTER, data=user_data, allow_redirects=False, timeout=30)
    # Expected to receive 302 redirect to login.php on success
    assert register_resp.status_code == 302
    location = register_resp.headers.get("Location", "")
    assert location.endswith("login.php")

test_post_myproject_register_php_with_valid_user_data_and_csrf_token()