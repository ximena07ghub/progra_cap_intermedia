<template>
  <div class="flex min-h-screen flex-col bg-aula-bg text-aula-cream">
    <StudentHeader v-if="session.role === 'estudiante'" />
    <WorkspaceHeader v-else mode="instructor" />

    <main class="mx-auto grid w-full max-w-aula flex-1 gap-0 px-6 py-10 lg:grid-cols-[320px_1fr] lg:px-8">
      <aside class="border border-white/10 bg-white/[0.02]">
        <div class="border-b border-white/10 p-5">
          <p class="font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-aula-green">Mensajería privada</p>
          <h1 class="mt-2 font-editorial text-3xl">Conversaciones</h1>
        </div>

        <button
          v-for="conversation in localConversations"
          :key="conversation.id"
          type="button"
          class="flex w-full gap-3 border-b border-white/10 p-4 text-left transition hover:bg-white/[0.04]"
          :class="conversation.id === activeId ? 'bg-white/[0.05]' : ''"
          @click="activeId = conversation.id"
        >
          <span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-aula-green-dark text-xs font-extrabold">{{ conversation.avatar }}</span>
          <span class="min-w-0">
            <strong class="block truncate text-sm">{{ conversation.participant }}</strong>
            <span class="mt-1 block truncate text-xs text-white/35">{{ conversation.course }}</span>
          </span>
        </button>
      </aside>

      <section class="flex min-h-[620px] flex-col border border-white/10 lg:border-l-0">
        <header class="border-b border-white/10 p-5">
          <strong class="block text-sm">{{ activeConversation.participant }}</strong>
          <span class="mt-1 block text-xs text-white/35">{{ activeConversation.participantRole }} · {{ activeConversation.course }}</span>
        </header>

        <div class="flex-1 space-y-4 overflow-y-auto p-5">
          <article
            v-for="message in activeConversation.messages"
            :key="message.id"
            class="max-w-[78%] p-4 text-sm leading-6"
            :class="message.from === 'me' ? 'ml-auto bg-aula-green-dark text-white' : 'bg-white/[0.05] text-white/75'"
          >
            <p>{{ message.text }}</p>
            <span class="mt-2 block text-[10px] uppercase tracking-[0.08em] text-white/35">{{ formatDate(message.date) }} · {{ message.time }}</span>
          </article>
        </div>

        <form class="grid gap-3 border-t border-white/10 p-5 sm:grid-cols-[1fr_auto]" @submit.prevent="sendMessage">
          <input v-model="draft" class="min-h-12 border border-white/10 bg-aula-bg px-4 text-sm text-white outline-none focus:border-aula-orange" placeholder="Escribe un mensaje privado" />
          <button type="submit" class="min-h-12 bg-aula-orange px-6 text-xs font-extrabold uppercase tracking-[0.08em] text-aula-bg">Enviar</button>
        </form>
      </section>
    </main>

    <PublicFooter />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import StudentHeader from '../../components/layout/StudentHeader.vue'
import WorkspaceHeader from '../../components/layout/WorkspaceHeader.vue'
import PublicFooter from '../../components/layout/PublicFooter.vue'
import { conversations, instructorConversations } from '../../data/mockPortal.js'
import { useSessionStore } from '../../stores/session.js'
import { formatDate } from '../../utils/format.js'

const session = useSessionStore()
const sourceConversations = session.role === 'instructor' ? instructorConversations : conversations
const localConversations = ref(structuredClone(sourceConversations))
const activeId = ref(localConversations.value[0].id)
const draft = ref('')

const activeConversation = computed(() => localConversations.value.find((item) => item.id === activeId.value) || localConversations.value[0])

function sendMessage() {
  const text = draft.value.trim()
  if (!text) return

  const now = new Date()
  activeConversation.value.messages.push({
    id: Date.now(),
    from: 'me',
    date: now.toISOString().slice(0, 10),
    time: now.toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit', hour12: false }),
    text,
  })
  draft.value = ''
}
</script>

