import { Expo } from 'expo-server-sdk';

// Instancia del cliente Expo
const expo = new Expo();

export async function sendPushNotification(token, { title, body, data = {} }) {
  if (!Expo.isExpoPushToken(token)) {
    console.error('❌ Token Expo inválido:', token);
    return;
  }

  const message = {
    to: token,
    sound: 'default',
    title,
    body,
    data,
  };

  try {
    const response = await expo.sendPushNotificationsAsync([message]);
    console.log('✅ Notificación enviada:', response);
    return response;
  } catch (error) {
    console.error('💥 Error al enviar notificación:', error);
  }
}
