import { boot } from 'quasar/wrappers'
import { Notify } from 'quasar'

export default boot(() => {
  // Configurar defaults de Notify
  Notify.setDefaults({
    position: 'top',
    timeout: 3000,
    textColor: 'white',
    actions: [{ icon: 'close', color: 'white' }]
  })
})
