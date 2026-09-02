import json
import os

import streamlit.components.v1 as components


_frontend_path = os.path.dirname(os.path.abspath(__file__))
streamlit_js_eval = components.declare_component("streamlit_js_eval", path=_frontend_path)


def _run(command, args=None, component_key=None):
    payload = json.dumps({"command": command, "args": args or {}}, separators=(",", ":"))
    key = component_key or f"{command}:{payload}"
    return streamlit_js_eval(js_expressions=payload, key=key)


def set_cookie(name, value, duration_days, component_key=None):
    return _run(
        "set_cookie",
        {"name": str(name), "value": str(value), "duration_days": int(duration_days)},
        component_key,
    )


def get_cookie(name, component_key=None):
    return _run("get_cookie", {"name": str(name)}, component_key or f"get_cookie:{name}")


def get_user_agent(component_key=None):
    return _run("get_user_agent", component_key=component_key or "UA")


def copy_to_clipboard(copiedText, linkText, successText, component_key=None):
    return _run(
        "copy_to_clipboard",
        {"copied_text": str(copiedText), "link_text": str(linkText), "success_text": str(successText)},
        component_key,
    )


def bootstrapButton(title, component_key=None):
    return _run("button", {"title": str(title)}, component_key or str(title))


def start_watching_location(component_key=None):
    return _run("start_watching_location", component_key=component_key or "start_watching_location")


def get_latest_location(component_key=None):
    return _run("get_latest_location", component_key=component_key or "get_latest_location")


def get_first_location(component_key=None):
    return _run("get_location", component_key=component_key or "get_location")


def get_browser_language(component_key=None):
    return _run("get_browser_language", component_key=component_key or "LANG")


def get_page_location(component_key=None):
    return _run("get_page_location", component_key=component_key or "LOC")


def create_share_link(sharedObject, linkText, successText, component_key=None):
    return _run(
        "share",
        {"share_data": sharedObject, "link_text": str(linkText), "success_text": str(successText)},
        component_key,
    )
