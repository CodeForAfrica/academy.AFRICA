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
}
