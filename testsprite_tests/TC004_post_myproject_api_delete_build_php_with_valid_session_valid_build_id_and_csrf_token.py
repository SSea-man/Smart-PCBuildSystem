import requests
from urllib.parse import urljoin

BASE_URL = "http://localhost:80/myproject/"
LOGIN_URL = urljoin(BASE_URL, "api/login.php")
SAVE_BUILD_URL = urljoin(BASE_URL, "api/save_build.php")
DELETE_BUILD_URL = urljoin(BASE_URL, "api/delete_build.php")

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"


def test_post_myproject_api_delete_build_php_with_valid_session_valid_build_id_and_csrf_token():
    session = requests.Session()
    timeout = 30

    # Authenticate via JSON API login to get session cookie
    login_payload = {"email": EMAIL, "password": PASSWORD}
    login_headers = {"Content-Type": "application/json"}
    login_resp = session.post(
        LOGIN_URL, json=login_payload, headers=login_headers, timeout=timeout
    )
    assert login_resp.status_code == 200
    login_json = login_resp.json()
    assert login_json.get("success") is True
    assert "user" in login_json

    # Create a new build to get a valid build_id
    # For components: we need valid component IDs; since no component IDs are given,
    # we will fetch some components from the store to use (category GPU).
    # But the instruction doesn't include endpoint for components, so instead,
    # we'll supply mock component IDs typical for test (assuming 1 through 5 are valid).
    # In real tests, this would fetch real component IDs.
    components_list = [1, 2, 3, 4, 5]
    save_build_payload = {
        "components": components_list,
        "total_bdt": 100000,
        "score": 85.5,
        "purpose": "Gaming",
        "name": "Test Build for Deletion",
        "fps": 60,
        "wattage": 450,
    }
    save_resp = session.post(
        SAVE_BUILD_URL,
        json=save_build_payload,
        timeout=timeout,
        headers={"Content-Type": "application/json"},
    )
    assert save_resp.status_code == 200
    save_json = save_resp.json()
    assert save_json.get("success") is True
    build_id = save_json.get("build_id")
    assert isinstance(build_id, int) and build_id > 0

    # Fetch CSRF token from a protected page or assume the token is in a cookie or session
    # Since no direct endpoint is given to retrieve csrf_token, try a GET to dashboard.php or use a fixed token if known.
    # Assume CSRF token is stored in a cookie named 'csrf_token' or from a GET to dashboard.php.
    # We'll try fetching dashboard.php to extract CSRF token from the response.

    DASHBOARD_URL = urljoin(BASE_URL, "dashboard.php")
    dash_resp = session.get(DASHBOARD_URL, timeout=timeout)
    assert dash_resp.status_code == 200
    # Try finding csrf_token in cookies or in response text as a hidden input
    csrf_token = None
    # Try cookie first
    csrf_token = session.cookies.get("csrf_token")
    if not csrf_token:
        # Fallback: try to parse from HTML input hidden field, simplistic:
        import re

        matches = re.findall(
            r'<input[^>]+name=["\']csrf_token["\'][^>]+value=["\']([^"\']+)["\']',
            dash_resp.text,
        )
        if matches:
            csrf_token = matches[0]
    assert csrf_token and isinstance(csrf_token, str) and len(csrf_token) > 0

    try:
        # Attempt to delete the build with valid session, build_id, and csrf_token
        delete_payload = {"build_id": build_id, "csrf_token": csrf_token}
        delete_headers = {"Content-Type": "application/x-www-form-urlencoded"}
        delete_resp = session.post(
            DELETE_BUILD_URL, data=delete_payload, headers=delete_headers, timeout=timeout, allow_redirects=False
        )

        # Check that response is a 302 redirect to dashboard.php after deletion
        assert delete_resp.status_code == 302
        location = delete_resp.headers.get("Location", "")
        assert location.endswith("dashboard.php")

        # Optionally confirm build no longer exists or deletion confirmed by absence (out of scope)
    finally:
        # Cleanup: in case deletion failed above, try to delete build again (best effort)
        try:
            cleanup_payload = {"build_id": build_id, "csrf_token": csrf_token}
            session.post(
                DELETE_BUILD_URL,
                data=cleanup_payload,
                headers={"Content-Type": "application/x-www-form-urlencoded"},
                timeout=timeout,
                allow_redirects=False,
            )
        except Exception:
            pass


test_post_myproject_api_delete_build_php_with_valid_session_valid_build_id_and_csrf_token()