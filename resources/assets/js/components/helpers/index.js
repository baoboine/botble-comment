function resize() {
    this.style.height = "auto";
    this.style.height = `${this.scrollHeight}px`;
}

function focus() {
    this.classList.add('focused');
    // this.removeEventListener("focus", focus);
}

export const setResizeListeners = ($el, query) => {
    const targets = $el.querySelectorAll(query);
    targets.forEach((target, i) => {
        target.style.height = `${target.scrollHeight}px`;
        target.addEventListener("input", resize);
        target.addEventListener("focus", focus);
    });
};
