<template>
    <div>
        <h2>Simulation</h2>

        <div class="workflow-toolbar">
            <select v-model="simulation">
                <option value="basic">Basic</option>
                <option value="complex">Complex</option>
            </select>
            <span>{{ current + 1 }} / {{ steps.length }}</span>
            <button @click="prev">Previous</button>
            <button @click="next">Next</button>

            <input id="graphviz" type="radio" value="graphviz" v-model="form.visualization">
            <label for="graphviz">Graphviz</label>
            <input id="jointjs" type="radio" value="jointjs" v-model="form.visualization">
            <label for="jointjs">JointJS</label>

            <p v-if="form.loading">Loading...</p>
        </div>

        <div v-if="step">
            <graphviz-visualization v-if="form.visualization == 'graphviz'" :step="step"></graphviz-visualization>
            <joint-visualization v-if="form.visualization == 'jointjs'" :step="step"></joint-visualization>
        </div>
    </div>
</template>

<script>
    import axios from 'axios'
    import GraphvizVisualization from './GraphvizVisualization.vue'
    import JointVisualization from './JointVisualization.vue'

    export default {
        components: {
            GraphvizVisualization,
            JointVisualization
        },

        data() {
            return {
                form: {
                    loading: false,
                    visualization: 'jointjs'
                },

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
                this.form.loading = true;

                axios.get('/api/simulation/' + this.simulation).then(response => {
                    this.current = 0;
                    this.form.loading = false;
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