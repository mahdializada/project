<template>
  <div class="work-area__container elevation-1">
    <div class="work-area__logo pa-1 me-1">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="45.568"
        height="45.397"
        class="logo__item-hover"
        viewBox="0 0 30.568 21.397"
      >
        <path
          id="general-design"
          d="M17.576,32H12.991a1.529,1.529,0,0,0-1.528,1.528v4.585a1.529,1.529,0,0,0,1.528,1.528h4.585A1.529,1.529,0,0,0,19.1,38.114V33.528A1.529,1.529,0,0,0,17.576,32ZM9.935,34.675H5.887a3.057,3.057,0,1,0,0,2.293H9.695a11.091,11.091,0,0,0-5.232,7.26H6.822a8.774,8.774,0,0,1,3.393-4.851,3.022,3.022,0,0,1-.281-1.263ZM7.642,45.755H3.057a1.529,1.529,0,0,0-1.528,1.528v4.585A1.529,1.529,0,0,0,3.057,53.4H7.642A1.529,1.529,0,0,0,9.17,51.869V47.284A1.529,1.529,0,0,0,7.642,45.755ZM27.511,32.764a3.054,3.054,0,0,0-2.83,1.91H20.633v3.439a3.03,3.03,0,0,1-.281,1.263,8.774,8.774,0,0,1,3.393,4.851H26.1a11.091,11.091,0,0,0-5.232-7.26h3.808a3.055,3.055,0,1,0,2.83-4.2Zm0,12.991H22.926A1.529,1.529,0,0,0,21.4,47.284v4.585A1.529,1.529,0,0,0,22.926,53.4h4.585a1.529,1.529,0,0,0,1.528-1.528V47.284A1.529,1.529,0,0,0,27.511,45.755Z"
          transform="translate(0 -32)"
        />
      </svg>
    </div>
    <div class="work-area__text ms-1">
      {{ $attrs.text }}
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
.work-area__container:hover {
  .work-area__logo {
    svg {
      transform: scale(1.05);
    }
  }
  .work-area__text {
    color: var(--v-primary-base);
  }
}
.work-area__container {
  padding: 16px;
  width: 300px;
  height: 80px;
  border-radius: 12px;
  margin: 8px;
  cursor: pointer;
  transition: all 0.3s;
  transform: scale(1);
  display: flex;
  align-items: center;
  .work-area__logo {
    background-color: #71717721;
    border-radius: 50%;
    width: 64px;
    height: 64px;
    svg {
      transition: all 0.3s;
      transform: scale(0.9);
    }
    path#general-design {
      fill: var(--v-primary-base);
    }
  }

  .work-area__text {
    font-weight: 500;
    white-space: nowrap;
    text-overflow: ellipsis;
    overflow: hidden;
    width: 240px;
    transition: all 0.3s;
    text-transform: capitalize;
  }
}
</style>
