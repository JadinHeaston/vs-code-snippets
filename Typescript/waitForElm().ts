//USAGE: waitForElm('<SELECTOR>').then((elm) => {<FUNCTION>});
async function waitForElm(selector: string) {
    return new Promise<HTMLElement | null>(resolve => {
        if (document.querySelector(selector)) {
            return resolve(document.querySelector(selector) as HTMLElement);
        }

        const observer = new MutationObserver(mutations => {
            if (document.querySelector(selector)) {
                resolve(document.querySelector(selector) as HTMLElement);
                observer.disconnect();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}