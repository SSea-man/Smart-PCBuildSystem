import requests
import re

BASE_URL = "http://localhost:80/myproject"
LOGIN_PAGE_URL = f"{BASE_URL}/login.php"
LOGIN_URL = f"{BASE_URL}/login.php"
CHATBOT_PROXY_URL = f"{BASE_URL}/api/chatbot_proxy.php"
TIMEOUT = 30

def test_post_myproject_api_chatbot_proxy_php_with_valid_session_and_valid_message():
    session = requests.Session()

    # Get login page to fetch CSRF token
    login_page_resp = session.get(LOGIN_PAGE_URL, timeout=TIMEOUT)
    assert login_page_resp.status_code == 200, f"Failed to get login page, status {login_page_resp.status_code}"

    # Parse CSRF token from login page HTML using regex
    match = re.search(r'name=["\']csrf_token["\']\s+value=["\']([^"\']+)["\']', login_page_resp.text)
    assert match is not None, "CSRF token input not found on login page"
    csrf_token = match.group(1)
    assert csrf_token, "CSRF token is empty"

    # Prepare login payload as form data
    login_payload = {
        'email': 'shadman1@pcbuild.com',
        'password': 'pass1234',
        'csrf_token': csrf_token
    }

    headers = {"Content-Type": "application/x-www-form-urlencoded"}

    # Authenticate to get session cookie
    login_resp = session.post(LOGIN_URL, data=login_payload, headers=headers, timeout=TIMEOUT, allow_redirects=False)
    assert login_resp.status_code == 302, f"Login failed with status {login_resp.status_code}"
    # Check redirect location
    redirect_location = login_resp.headers.get('Location', '')
    assert redirect_location.endswith('dashboard.php'), f"Login redirect location unexpected: {redirect_location}"

    # Prepare chatbot message payload
    message_payload = {"message": "Hello, can you help me build a gaming PC?"}

    # Post message to chatbot_proxy.php using authenticated session
    chat_resp = session.post(CHATBOT_PROXY_URL, json=message_payload, timeout=TIMEOUT)

    assert chat_resp.status_code == 200, f"Chatbot proxy returned unexpected status {chat_resp.status_code}"
    chat_json = chat_resp.json()
    assert "reply" in chat_json, "Response JSON missing 'reply' key"
    assert isinstance(chat_json["reply"], str), "'reply' value is not a string"
    assert len(chat_json["reply"].strip()) > 0, "'reply' string is empty"


test_post_myproject_api_chatbot_proxy_php_with_valid_session_and_valid_message()
