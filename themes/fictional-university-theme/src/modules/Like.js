import axios from "axios"

class Like {
    constructor() {
        if (document.querySelector(".like-box")) {
            axios.defaults.headers.common["X-WP-Nonce"] = MYSCRIPT.nonce
            this.events()
        }
    }

    events() {
        document.querySelector(".like-box").addEventListener("click", e => this.ourClickDispatcher(e))
    }


    ourClickDispatcher(e) {
        let currentLikeBox = e.target
        while (!currentLikeBox.classList.contains("like-box")) {
            currentLikeBox = currentLikeBox.parentElement
        }

        if (currentLikeBox.getAttribute("data-exists") == "yes") {
            this.deleteLike(currentLikeBox)
        } else {
            this.createLike(currentLikeBox)
        }
    }

    async createLike(currentLikeBox) {
        try {
            const response = await axios.post(
                MYSCRIPT.site_url + "/wp-json/university/v1/manageLike",
                { "professorID": currentLikeBox.getAttribute("data-professor") })
            if (response.data.success === 1) {
                currentLikeBox.setAttribute("data-exists", "yes")
                var likeCount = parseInt(currentLikeBox.querySelector(".like-count").innerHTML, 10)
                likeCount++
                currentLikeBox.querySelector(".like-count").innerHTML = likeCount
                currentLikeBox.setAttribute("data-like", response.data.likeID)
            }
            console.log(response.data)
        } catch (e) {
            console.log("Sorry")
        }
    }

    async deleteLike(currentLikeBox) {
        try {
            const response = await axios({
                url: MYSCRIPT.site_url + "/wp-json/university/v1/manageLike",
                method: 'delete',
                data: { "like": currentLikeBox.getAttribute("data-like") },
            })
            if (response.data.success === 1) {
                currentLikeBox.setAttribute("data-exists", "no")
                var likeCount = parseInt(currentLikeBox.querySelector(".like-count").innerHTML, 10)
                likeCount--
                currentLikeBox.querySelector(".like-count").innerHTML = likeCount
                currentLikeBox.setAttribute("data-like", "")
            }
            console.log(response.data)
        } catch (e) {
            console.log(e)
        }
    }
}

export default Like