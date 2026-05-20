import requests

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/login.php"
CHATBOT_PROXY_URL = f"{BASE_URL}/api/chatbot_proxy.php"
LOGIN_EMAIL = "shadman1@pcbuild.com"
LOGIN_PASSWORD = "pass1234"
TIMEOUT = 30

def test_post_chatbot_proxy_with_valid_session_and_message():
    session = requests.Session()
    try:
        # Step 1: Get login page to obtain CSRF token (assuming token is in HTML as hidden input named csrf_token)
        # Because CSRF tokens are required for POST forms but not JSON API calls, 
        # login.php is a form POST, so we must get the CSRF token first.
        response = session.get(LOGIN_URL, timeout=TIMEOUT)
        assert response.status_code == 200, "Failed to load login page for CSRF token"

        import re
        # Extract csrf_token from the login page HTML
        csrf_token_search = re.search(r'name="csrf_token"\s+value="([a-zA-Z0-9_\-]+)"', response.text)
        assert csrf_token_search, "CSRF token not found in login page"
        csrf_token = csrf_token_search.group(1)

        # Step 2: POST login credentials with CSRF token
        login_data = {
            "email": LOGIN_EMAIL,
            "password": LOGIN_PASSWORD,
            "csrf_token": csrf_token
        }
        login_response = session.post(LOGIN_URL, data=login_data, allow_redirects=False, timeout=TIMEOUT)

        # Expected 302 redirect to dashboard.php on successful login
        assert login_response.status_code == 302, f"Unexpected login response status: {login_response.status_code}"
        redirect_location = login_response.headers.get("Location", "")
        assert "dashboard.php" in redirect_location, f"Unexpected redirect location after login: {redirect_location}"

        # Step 3: POST chatbot_proxy.php with valid session cookie and message
        message_payload = {
            "message": "Hello AI, can you help me build a PC?"
        }
        chatbot_response = session.post(CHATBOT_PROXY_URL, json=message_payload, timeout=TIMEOUT)

        # Validate response status and content
        assert chatbot_response.status_code == 200, f"Chatbot proxy response status is not 200: {chatbot_response.status_code}"

        json_response = chatbot_response.json()
        assert "reply" in json_response, "Response JSON missing 'reply' field"
        assert isinstance(json_response["reply"], str), "'reply' field is not a string"
        assert len(json_response["reply"]) > 0, "'reply' field is empty"

    finally:
        session.close()

test_post_chatbot_proxy_with_valid_session_and_message()