function resize() {
    this.style.height = 'auto';
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

export function linkify(href) {
    // http://, https://, ftp://
    const urlPattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;

    // www. sans http:// or https://
    const pseudoUrlPattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;

    // Email addresses
    const emailAddressPattern = /[\w.]+@[a-zA-Z_-]+?(?:\.[a-zA-Z]{2,6})+/gim;

    return href
        .replace(urlPattern, '<a target="_blank" ref="nofollow" href="$&">$&</a>')
        .replace(pseudoUrlPattern, '$1<a target="_blank" ref="nofollow" href="http://$2">$2</a>')
        .replace(emailAddressPattern, '<a href="mailto:$&">$&</a>');
}
