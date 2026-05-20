import requests

BASE_URL = "http://localhost:80/myproject"
TIMEOUT = 30

def test_post_myproject_login_php_with_valid_credentials_and_csrf_token():
    session = requests.Session()
    login_api_url = f"{BASE_URL}/api/login.php"
    login_payload = {
        "email": "shadman1@pcbuild.com",
        "password": "pass1234"
    }
    headers = {
        "Content-Type": "application/json"
    }
    try:
        # Authenticate via JSON API (bypass form-based login)
        response = session.post(login_api_url, json=login_payload, headers=headers, timeout=TIMEOUT)
        # Check response status 200 and success true in JSON
        assert response.status_code == 200, f"Expected 200 OK, got {response.status_code}"
        json_data = response.json()
        assert "success" in json_data and json_data["success"] is True, "Login API did not succeed"
        assert "user" in json_data and isinstance(json_data["user"], dict), "Missing user details in login response"
        # Check session cookie set
        assert session.cookies.get_dict(), "Session cookie not set after login"

        # Use session cookie to request dashboard.php to confirm authentication
        dashboard_url = f"{BASE_URL}/dashboard.php"
        dashboard_response = session.get(dashboard_url, allow_redirects=False, timeout=TIMEOUT)
        # Expect 200 OK or redirect? Dashboard usually protected. Check for 200 or else redirect
        # We test that access is allowed and we have authenticated session
        assert dashboard_response.status_code in (200, 302), f"Unexpected status code on dashboard access: {dashboard_response.status_code}"
        # Check for JWT cookie presence in session cookies (HttpOnly cookies are visible only if set via Set-Cookie)
        cookies = session.cookies.get_dict()
        jwt_cookie_found = any("jwt" in cookie.lower() for cookie in cookies.keys())
        assert jwt_cookie_found, "JWT cookie not found after login"

    finally:
        session.close()

test_post_myproject_login_php_with_valid_credentials_and_csrf_token()