<template>
    <div class="">
        <p>
            <span v-for="word in writtenWords">
            {{ word }}
            </span>
        </p>
    </div>
</template>

<script>
export default {
    name: 'WriteChatComponent',
    props: {
        textToShow: String,
        interval: Number,
        timeout: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            words: [],
            writtenWords: [],
            index: 0
        }
    },
    mounted: function () {
        if(this.timeout == 0) {
            this.writeText();
        }
        else {
            setTimeout(this.writeText, this.timeout);
        }
    },
    methods: {
        writeText() {
            this.words = this.textToShow.split(' ');
            this.index = 0;
            this.writeWord();
        },
        writeWord() {
            if (this.index < this.words.length) {
                this.writtenWords[this.index] = this.words[this.index] + ' ';
                this.index++;
                setTimeout(this.writeWord, this.interval);
            }
        }
    },
}
</script>

<style scoped>
</style>