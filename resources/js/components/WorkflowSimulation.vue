<template>
    <div>
        <h2>Simulations</h2>

        <div class="workflow-toolbar">
            <select v-model="simulation">
                <option value="basic">Basic</option>
                <option value="complex">Complex</option>
            </select>
            <button @click="prev">Previous</button>
            <span>{{ current + 1 }} / {{ steps.length }}</span>
            <button @click="next">Next</button>
        </div>

        <div v-if="step">
            <h3>✉ {{ step.heading }}</h3>
            <p v-if="step.summary" v-html="step.summary"></p>
            <img width="700" :src="step.graph" />
            <h5>Case Attributes</h5>
            <pre>{{ step.attributes }}</pre>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'

    export default {
        data() {
            return {
                simulation: null,
                current: 0,
                steps: []
            }
        },

        computed: {
            step() {
                return this.steps[this.current]
            }
        },

        created() {
            this.simulation = 'basic'
        },

        watch: {
            simulation(sim) {
                axios.get('/api/simulation/' + this.simulation).then(response => {
                    this.current = 0;
                    this.steps = response.data
                });
            }
        },

        methods: {
            next() {
                this.current = (this.current + 1) % this.steps.length
            },

            prev() {
                if (this.current - 1 < 0) {
                    this.current = this.steps.length - 1;
                    return;
                }

                this.current--;
            }
        }
    }
</script>