import { ComputedRef, Ref } from 'vue'
export type LayoutKey = "main-layout" | "upload-layout"
declare module "/Users/noa/PhpstormProjects/tiktok-clone-api/node_modules/nuxt/dist/pages/runtime/composables" {
  interface PageMeta {
    layout?: false | LayoutKey | Ref<LayoutKey> | ComputedRef<LayoutKey>
  }
}