import requests

BASE_URL = "http://localhost:80/myproject/"
LOGIN_API = BASE_URL + "api/login.php"
LOGOUT_PAGE = BASE_URL + "logout.php"

def test_get_myproject_logout_php_with_authenticated_session():
    session = requests.Session()
    try:
        # Authenticate using JSON API login to get session cookie
        login_payload = {"email": "shadman1@pcbuild.com", "password": "pass1234"}
        login_headers = {"Content-Type": "application/json"}
        login_resp = session.post(LOGIN_API, json=login_payload, headers=login_headers, timeout=30)
        assert login_resp.status_code == 200, f"Login failed with status {login_resp.status_code}"
        json_data = login_resp.json()
        assert json_data.get("success") is True, "Login API did not return success true"
        # The session cookie should now be set in session.cookies

        # Perform GET /logout.php with authenticated session
        logout_resp = session.get(LOGOUT_PAGE, allow_redirects=False, timeout=30)

        # Verify 302 redirect to index.php
        assert logout_resp.status_code == 302, f"Expected 302 redirect, got {logout_resp.status_code}"
        location = logout_resp.headers.get("Location", "")
        assert location.endswith("index.php") or location == "index.php", f"Expected redirect to index.php, got {location}"

        # Check session cookie cleared or expired - typical is Set-Cookie header with expiry in past
        set_cookie_headers = logout_resp.headers.get("Set-Cookie", "")
        assert "jwt" in set_cookie_headers.lower() or "session" in set_cookie_headers.lower() or set_cookie_headers != "", "Expected Set-Cookie header to clear JWT or session cookie"

    finally:
        session.close()

test_get_myproject_logout_php_with_authenticated_session()