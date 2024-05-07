function openModal(type) {
  const anchor = document.getElementById(`${type}-modal`);
  if (anchor) {
    anchor.style.visibility = "visible";
  }
  const overlay = document.getElementById(`${type}-modal-bg`);
  if (overlay) {
    overlay.style.backgroundColor = "black";
    overlay.style.opacity = "0.7";
    overlay.style.visibility = "visible";
  }
  const content = document.getElementById(`${type}-modal-content`);
  if (content) {
    content.style.visibility = "visible";
    content.style.transform = "translateX(-50%) translateY(-50%) scale(1)";
    content.style.top = "50%";
    content.style.left = "50%";
  }
}

function closeModal(type) {
  const anchor = document.getElementById(`${type}-modal`);
  if (anchor) {
    anchor.style.visibility = "hidden";
  }
  const overlay = document.getElementById(`${type}-modal-bg`);
  if (overlay) {
    overlay.style.visibility = "hidden";
  }
  const content = document.getElementById(`${type}-modal-content`);
  if (content) {
    content.style.transform = "scale(0)";
    content.style.top = 0;
  }
  window.location.hash = "";
}

window.onload = function () {
  const { pathname, search } = window.location;
  const isLoginFailed = new URLSearchParams(search).get("login")==="failed"
  if (isLoginFailed) {
    if (pathname === "/login/") {
      document.getElementById("login_error").innerText =
        "Error: An error occurred, either the password you entered is incorrect, the email is incorrect or your account is not activated";
    } else {

      window.location.href = `/login/?login=failed&redirect_url=${pathname}`
    }
    return;
  }
};

document.onkeydown = function (event) {
  const logins = ["/login/", "/login", "/register/"]
  if(logins.includes(window.location.pathname)){
    event = event || window.event;
    if (event.keyCode === 27) {
      event.preventDefault();
      window.location.href = "/"
    }
  }
}
