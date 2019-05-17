class Stuttering {
    constructor() {
        this.title = $('.main-title')
        this.titleContent = "Creez votre compte !"
        this.titleLength = this.titleContent.replace(/\s/g, "").length
        this.numberOfTimes = 0
        this.print = window.setTimeout(() => {
            this.printQuestion()
        }, 2500)
        this.account = window.setTimeout(() => {
            window.setInterval(() => {
                this.getRandomNumber()
            }, 1000)
        }, 5000)
        

        
    }
    
    getRandomNumber() {
        this.randomNumber = Math.floor(Math.random() * this.titleLength) + 1;
        this.secondRandomNumber = Math.floor(Math.random() * this.titleLength) + 1;
        this.glitchingLetter = this.titleContent.charAt(this.randomNumber)
        this.newTitle = this.titleContent.replace(this.glitchingLetter, this.titleContent.charAt(this.secondRandomNumber))
        this.newTitleContent = this.newTitle
        document.getElementById('glitch-title').innerHTML = this.newTitleContent
    }


    printQuestion() {
            document.getElementById('glitch-title').innerHTML = "Des milliers de réponses"
    }
}

var newStuttering = new Stuttering()