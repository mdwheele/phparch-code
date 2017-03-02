<template>
    <div>
        <h3>✉ {{ step.heading }}</h3>
        <p v-if="step.summary" v-html="step.summary"></p>
        <div class="jointjs-visualization box"></div>
        <h5>Case Attributes</h5>
        <pre>{{ step.attributes }}</pre>
    </div>
</template>

<script>
    import joint from 'jointjs'

    export default {
        data() {
            return {
                graph: null,
                paper: null,
            }
        },

        props: ['step'],

        mounted() {
            this.graph = new joint.dia.Graph

            this.paper = new joint.dia.Paper({
                el: this.$el.querySelector('.jointjs-visualization'),
                width: this.$el.offsetWidth,
                height: this.$el.offsetHeight,
                model: this.graph,
                gridSize: 1
            })

            this.render(this.step)
        },

        watch: {
            step(value) {
                this.render(value);
            }
        },

        methods: {
            render() {
                this.graph.clear()
                let g = this.step.graph

                _.each(g.nodes, (el) => {
                    console.log(el)

                    let maxLineLength = _.max(el.label.split('\n'), l => { return l.length; }).length
                    let letterSize = 12
                    let width = 2 * (letterSize * (0.5 * maxLineLength + 1))
                    let height = 2 * ((el.label.split('\n').length + 1) * letterSize)

                    let node = null;

                    if (el.type == 'task') {
                        node = new joint.shapes.basic.Rect({
                            id: el.id,
                            size: { width: width, height: height },
                            attrs: {
                                text: { text: el.label, 'font-size': letterSize, 'font-family': 'monospace' },
                            }
                        })
                    } else {
                        node = new joint.shapes.basic.Circle({
                            id: el.id,
                            attrs: {
                                circle: {
                                    'stroke-width': 2
                                },
                                text: { text: el.label, 'font-size': letterSize, 'font-family': 'monospace' },
                            }
                        })
                    }

                    if (el.type == 'source' || el.type == 'sink') {
                        node.attr({
                            circle: {
                                fill: 'grey'
                            }
                        })
                    }

                    if (el.marked) {
                        node.attr({
                            circle: {
                                stroke: 'red'
                            }
                        })
                    }

                    this.graph.addCell(node)
                })

                _.each(g.edges, (el) => {
                    this.graph.addCell(new joint.dia.Link({
                        source: { id: el.from },
                        target: { id: el.to },
                        attrs: {
                            '.marker-arrowheads, .link-tools': {
                                display: "none"
                            }
                        }
                    }))
                })

                joint.layout.DirectedGraph.layout(this.graph, { setLinkVertices: false, rankDir: 'LR' })
                this.paper.fitToContent()
            }
        }
    }
</script>