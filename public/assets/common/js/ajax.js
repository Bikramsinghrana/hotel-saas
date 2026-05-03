// Minimal ajax helper (uses fetch)
window.Ajax = {
  request(url, options = {}){
    return fetch(url, options).then(r=>r.json());
  }
};
