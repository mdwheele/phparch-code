<template>
    <div>
        <h1 v-text="message"></h1>

        <select v-model="simulation">
            <option value="basic">Basic</option>
            <option value="complex">Complex</option>
        </select>

        <div v-for="step in steps">
            <h3>✉ {{ step.heading }}</h3>
            <p v-if="step.summary">&ldquo;{{ step.summary}}&rdquo;</p>
            <img width="700" :src="step.graph" />
            <h5>Case Attributes</h5>
            <pre>{{ step.attributes }}</pre>
            <hr>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        data() {
            return {
                message: 'Hello, Vue!',
                simulation: 'basic'
            }
        },

        asyncComputed: {
            steps() {
                let steps = [];
                axios.get('/api/simulation/' + this.simulation).then(response => this.steps = response.data);
                return steps;
            }
        }
    }
</script>