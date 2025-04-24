<template>
    <div>

        <PreLoader v-if="$store.state.isLoading" />

        <keep-alive>
            <DAX :page="$route.name" :user_id="user_id" :vector_id="vector_id"  v-if="$route.meta.showDax" />
        </keep-alive>

        <!-- The Layout -->
        <router-view />
        <!-- This will now load Home.vue, Login.vue, Register.vue -->
    </div>
</template>
<script>
import { computed } from 'vue'
import { useStore } from 'vuex'
import PreLoader from './sections/PreLoader.vue'
import DAX from './sections/DAX.vue'

export default {
    name: "App",
    components: { PreLoader, DAX },
    setup() {
        const store = useStore()
        // reactive reference to store.state.user.id
        const user_id = computed(() => store.state.user_id)


        const vector_id = computed(() => store.state.vector_id)

        return { user_id, vector_id }
    },
    mounted() {
        this.$store.commit("setLoading", true)
        setTimeout(() => this.$store.commit("setLoading", false), 3000)
    }
}
</script>