import requests
import re

BASE_URL = "http://localhost:80/myproject"
LOGIN_URL = f"{BASE_URL}/login.php"
SAVE_BUILD_URL = f"{BASE_URL}/api/save_build.php"
DELETE_BUILD_URL = f"{BASE_URL}/api/delete_build.php"
GET_COMPONENTS_URL = f"{BASE_URL}/api/get_components.php"

EMAIL = "shadman1@pcbuild.com"
PASSWORD = "pass1234"

TIMEOUT = 30

def extract_csrf_token(html_text):
    # Extract csrf_token value from hidden input in HTML form
    match = re.search(r'<input[^>]+name=["\']csrf_token["\'][^>]+value=["\']([^"\']+)["\']', html_text)
    if match:
        return match.group(1)
    return None

def test_post_save_build_with_valid_session_and_build_data():
    session = requests.Session()

    # 1. GET login.php to retrieve a valid CSRF token
    get_resp = session.get(LOGIN_URL, timeout=TIMEOUT)
    assert get_resp.status_code == 200, f"Failed to load login page, status {get_resp.status_code}"
    csrf_token = extract_csrf_token(get_resp.text)
    assert csrf_token, "Failed to extract CSRF token from login page"

    # 2. Login to obtain authenticated session and cookies
    login_data = {
        "email": EMAIL,
        "password": PASSWORD,
        "csrf_token": csrf_token,
    }
    login_resp = session.post(LOGIN_URL, data=login_data, allow_redirects=False, timeout=TIMEOUT)
    # Expect 302 redirect to dashboard.php
    assert login_resp.status_code == 302, f"Login failed, expected 302 but got {login_resp.status_code}"
    location = login_resp.headers.get("Location", "")
    assert "dashboard.php" in location, f"Login redirect location unexpected: {location}"

    build_id = None
    try:
        # 3. To prepare build data, fetch valid component IDs
        def fetch_components(category, max_budget=100000):
            resp = session.post(
                GET_COMPONENTS_URL,
                json={"category": category, "budget_max": max_budget},
                timeout=TIMEOUT,
            )
            assert resp.status_code == 200, f"Failed to fetch components for {category}"
            comp_list = resp.json()
            assert isinstance(comp_list, list), f"Components response not a list for {category}"
            return comp_list

        cpu_comps = fetch_components("CPU")
        mb_comps = fetch_components("Motherboard")
        gpu_comps = fetch_components("GPU")
        ram_comps = fetch_components("RAM")
        psu_comps = fetch_components("PSU")

        assert cpu_comps, "No CPU components available"
        assert mb_comps, "No Motherboard components available"
        assert gpu_comps, "No GPU components available"
        assert ram_comps, "No RAM components available"
        assert psu_comps, "No PSU components available"

        components = [
            cpu_comps[0]["id"],
            mb_comps[0]["id"],
            gpu_comps[0]["id"],
            ram_comps[0]["id"],
            psu_comps[0]["id"],
        ]

        total_bdt = 100000.0
        score = 95.5
        purpose = "gaming"
        name = "Test Build TC003"
        fps = 120
        wattage = 450

        save_build_payload = {
            "components": components,
            "total_bdt": total_bdt,
            "score": score,
            "purpose": purpose,
            "name": name,
            "fps": fps,
            "wattage": wattage,
        }

        # 4. POST save_build.php with build data
        save_resp = session.post(SAVE_BUILD_URL, json=save_build_payload, timeout=TIMEOUT)
        assert save_resp.status_code == 200, f"Save build failed with status code {save_resp.status_code}"

        save_resp_json = save_resp.json()
        assert "success" in save_resp_json and save_resp_json["success"] is True, f"Save build success false or missing in response: {save_resp_json}"
        assert "build_id" in save_resp_json and isinstance(save_resp_json["build_id"], int), f"build_id missing or invalid in response: {save_resp_json}"

        build_id = save_resp_json["build_id"]

    finally:
        if build_id is not None:
            # Need to get a valid csrf_token for delete as well
            # Since API doesn't provide CSRF, get dashboard page to fetch new token
            dashboard_url = f"{BASE_URL}/dashboard.php"
            dash_resp = session.get(dashboard_url, timeout=TIMEOUT)
            if dash_resp.status_code == 200:
                del_csrf_token = extract_csrf_token(dash_resp.text) or csrf_token
            else:
                del_csrf_token = csrf_token  # fallback to old token

            delete_payload = {
                "build_id": build_id,
                "csrf_token": del_csrf_token,
            }
            del_resp = session.post(DELETE_BUILD_URL, data=delete_payload, allow_redirects=False, timeout=TIMEOUT)
            if del_resp.status_code != 302 or "dashboard.php" not in del_resp.headers.get("Location", ""):
                raise AssertionError(
                    f"Cleanup delete build failed: status {del_resp.status_code} location {del_resp.headers.get('Location', '')}"
                )


test_post_save_build_with_valid_session_and_build_data()
