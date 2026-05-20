import requests

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/api/login.php"
CHECK_COMPATIBILITY_URL = f"{BASE_URL}/api/check_compatibility.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"
TIMEOUT = 30

def test_post_api_check_compatibility_with_valid_session_and_components_map():
    session = requests.Session()
    # Authenticate using JSON API login to get session cookie
    login_payload = {"email": EMAIL, "password": PASSWORD}
    login_headers = {"Content-Type": "application/json"}
    login_resp = session.post(LOGIN_URL, json=login_payload, headers=login_headers, timeout=TIMEOUT)
    assert login_resp.status_code == 200, f"Login failed with status code {login_resp.status_code}"
    login_json = login_resp.json()
    assert login_json.get("success") is True, "Login response indicates failure"

    try:
        # To build a valid compatible components map, fetch at least one component from each category
        # We'll fetch one component per category: CPU, Motherboard, GPU, RAM, PSU
        categories = ["CPU", "Motherboard", "GPU", "RAM", "PSU"]
        components_map = {}
        for category in categories:
            # POST get_components.php with filter category=category
            payload = {"category": category}
            resp = session.post(GET_COMPONENTS_URL, json=payload, timeout=TIMEOUT)
            assert resp.status_code == 200, f"Get components for {category} failed with status {resp.status_code}"
            components = resp.json()
            assert isinstance(components, list) and len(components) > 0, f"No components found for category {category}"
            # Pick the first component ID from the list
            comp_id = components[0].get("id")
            assert isinstance(comp_id, int) and comp_id > 0, f"Invalid component id for category {category}"
            components_map[category] = comp_id

        # POST to check_compatibility.php with the complete components map
        check_payload = {"components": components_map}
        check_headers = {"Content-Type": "application/json"}
        compatibility_resp = session.post(CHECK_COMPATIBILITY_URL, json=check_payload, headers=check_headers, timeout=TIMEOUT)
        assert compatibility_resp.status_code == 200, f"Compatibility check failed with status {compatibility_resp.status_code}"
        compatibility_json = compatibility_resp.json()
        # Verify response keys and values
        assert "compatible" in compatibility_json, "'compatible' key missing in response"
        assert compatibility_json["compatible"] is True, "Components reported as incompatible but expected compatible"
        assert "issues" in compatibility_json, "'issues' key missing in response"
        assert isinstance(compatibility_json["issues"], list), "'issues' is not a list"
        assert len(compatibility_json["issues"]) == 0, f"Issues reported: {compatibility_json['issues']}"
    finally:
        session.close()

test_post_api_check_compatibility_with_valid_session_and_components_map()