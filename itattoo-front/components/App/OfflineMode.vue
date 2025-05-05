<template>
  <div :class="{ active: $nuxt.isOffline }" class="offlineMode">
    <div class="info">
      <span>
        <div class="wifi">
          <div class="semione"></div>
          <div class="semitwo"></div>
          <div class="semithree"></div>
          <div class="circle"></div>
        </div>
      </span>
      <div>You are currently offline</div>
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style scoped lang="scss">
.offlineMode {
  display: flex;
  height: 0;
  width: 100vw;
  position: fixed;
  bottom: 0;
  left: 0;
  justify-content: center;
  z-index: 999999999999999999999;
  background: white;
  flex-direction: column;
  align-items: center;
  transition: 2s;
  overflow: hidden;
  transition: height 0.25s;
  &.active {
    height: 100vh;
    transition: height 0.25s;
  }
  .info {
    display: flex;
    align-items: center;
    flex-direction: column;
    justify-content: center;
    > span {
      display: flex;
      align-items: center;
      justify-content: center;
      background: #eb5757;
      border-radius: 50%;
      box-shadow: 0 0 0 0 rgba(235, 87, 87, 1);
      margin: 40px;
      height: 160px;
      width: 160px;
      padding-top: 44px;
      position: relative;
      &:before {
        content: '';
        position: absolute;
        display: block;
        width: 200%;
        height: 200%;
        box-sizing: border-box;
        margin-left: 0%;
        margin-top: -44px;
        border-radius: 50%;
        background-color: rgba(235, 87, 87, 0.3);
        animation: pulse-ring 2.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
      }
      @keyframes pulse-ring {
        0% {
          transform: scale(0.33);
        }
        80%,
        100% {
          opacity: 0;
        }
      }

      .wifi {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
      }
      .wifi .semione {
        position: absolute;
        width: 120px;
        aspect-ratio: 1;
        border: 8px solid transparent;
        border-top-color: white;
        border-radius: 50%;
      }
      .wifi .semitwo {
        position: absolute;
        width: 80px;
        aspect-ratio: 1;
        border: 6px solid transparent;
        border-top-color: white;
        border-radius: 50%;
      }
      .wifi .semithree {
        position: absolute;
        width: 45px;
        aspect-ratio: 1;
        border: 4px solid transparent;
        border-top-color: white;
        border-radius: 50%;
      }
      .wifi .circle {
        position: absolute;
        width: 15px;
        aspect-ratio: 1;
        background-color: white;
        border-radius: 50%;
      }
      .wifi:hover .semione {
        animation: semione 2s;
      }
      @keyframes semione {
        0%,
        100% {
          transform: translate(0);
        }
        10% {
          transform: translateY(40px);
        }
        15% {
          transform: translateY(30px);
        }
        25% {
          transform: translateY(30px) translateX(-5px);
        }
        35% {
          transform: translateY(30px) translateX(0);
        }
        45% {
          transform: translateY(30px) translateX(5px);
        }
        55% {
          transform: translateY(30px) translateX(0);
        }
        65% {
          transform: translateY(55px);
        }
        75% {
          transform: translate(0);
        }
      }
      .wifi:hover .semitwo {
        animation: semitwo 2s;
      }
      @keyframes semitwo {
        0%,
        100% {
          transform: rotate(0) translate(0);
        }
        8% {
          transform: rotate(180deg) translateY(24px);
        }
        15% {
          transform: rotate(180deg) translateY(24px);
        }
        25% {
          transform: rotate(180deg) translateY(24px) translateX(5px);
        }
        35% {
          transform: rotate(180deg) translateY(32px) translateX(0);
        }
        45% {
          transform: rotate(180deg) translateY(32px) translateX(-5px);
        }
        55% {
          transform: rotate(180deg) translateY(24px) translateX(0);
        }
        65% {
          transform: rotate(180deg) translateY(24px) translateX(0);
        }
        75% {
          transform: rotate(360deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }
      .wifi:hover .semithree {
        animation: semithree 2s;
      }
      @keyframes semithree {
        0%,
        100% {
          transform: translate(0);
        }
        10% {
          transform: translateY(22px);
        }
        15% {
          transform: translateY(8px);
        }
        25% {
          transform: translateY(8px) translateX(-5px);
        }
        35% {
          transform: translateY(8px) translateX(0);
        }
        45% {
          transform: translateY(8px) translateX(5px);
        }
        55% {
          transform: translateY(8px) translateX(0);
        }
        65% {
          transform: translateY(22px);
        }
        75% {
          transform: translate(0);
        }
      }
      .wifi:hover .circle {
        animation: circle 2s;
      }
      @keyframes circle {
        0%,
        100% {
          transform: translate(0);
        }
        10% {
          transform: translateY(5px);
        }
        15% {
          transform: translateY(0);
        }
        25% {
          transform: translateX(-10px);
        }
        35% {
          transform: translateY(-5px) translateX(0);
        }
        45% {
          transform: translateY(0) translateX(10px);
        }
        55% {
          transform: translate(0);
        }
        65% {
          transform: translateY(5px);
        }
        75% {
          transform: translate(0);
        }
      }
    }
    > div {
      font-size: 21px;
      font-weight: 600;
      color: gray;
      margin-top: 2rem;
    }
  }
}
</style>
