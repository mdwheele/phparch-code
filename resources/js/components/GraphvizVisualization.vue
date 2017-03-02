<template>
    <div class="graphviz-visualization">
        <h3>✉ {{ step.heading }}</h3>
        <p v-if="step.summary" v-html="step.summary"></p>
        <img width="700" :src="step.img" />
        <h5>Case Attributes</h5>
        <pre>{{ step.attributes }}</pre>
    </div>
</template>

<script>
    export default {
        props: ['step']
    }
</script>