<script>
import { h, defineComponent } from "vue";

export default defineComponent({
    props: ["text"],
    emits: ["tag"],
    methods: {
        splitAt(slicable, indices) {
            return [0, ...indices].map((n, i, m) => slicable.slice(n, m[i + 1]));
        },
        goToTags() {
            this.$inertia.visit(route("tag.index"));
        },
    },
    computed: {
        renderText() {
            //const tags = _.uniq(this.$s.getTags(this.text));
            var tags = this.$s.getTags(this.text, "matchAll");
            var text = JSON.parse(JSON.stringify(this.text));
            const indexes = [];
            for (const ocurrencia of tags) {
                indexes.push(ocurrencia.index);
                indexes.push(ocurrencia.index + ocurrencia[0].length);
            }
            //console.log(tags, indexes, this.splitAt(text, indexes));

            //tags.map(x => text = text.replaceAll(x, '<span class=" font-bold text-blue-500 underline cursor-pointer" @click="emit(`tag`, x)">' + x + '</span>'))
            return this.splitAt(text, indexes);
        },
    },
    render() {
        return h(
            "p",
            { class: "whitespace-pre-line" },
            this.renderText.map((x) => {
                if (x.search(/^(\#|\$|\*|\@|\!|\?|\+)+/) === 0) {
                    return h(
                        "span",
                        {
                            class: "font-bold text-blue-500 underline cursor-pointer",
                            oncontextmenu:(e) => {e.preventDefault(); this.goToTags()},
                            onClick: () => {
                                this.$emit("tag", x);
                            },
                        },
                        x
                    );
                } else {
                    return x;
                }
            })
        );
    },
});
</script>
