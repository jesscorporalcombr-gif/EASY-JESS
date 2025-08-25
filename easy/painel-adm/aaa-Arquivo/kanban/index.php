

<!DOCTYPE html>
<html lang="pt-br">
<head>

  <style>
      
        html {
          box-sizing: border-box;

        }

        :root {
          --column-1: #fb8791;
          --column-2: #b8f9a4;
          --column-3: #b6eaf9;
          --column-4: #fef28f;
        }

        body {
          margin: 0;
          background: url('../../img/banner_kanban.jpg');
          background-size: cover;
          background-position: 50% 60%;
          background-attachment: fixed;
          color: white;
          overflow-y: hidden;
        }

        h1 {
          letter-spacing: 2px;
          text-shadow: 1px 1px 1px grey;
          color: white;
          padding: 2px;
        }



        .main-title {
          text-align: center;
          font-size: 3rem;
        }

        ul {
          list-style-type: none;
          margin: 0;
          padding: 0;
        }

        .drag-container {
          margin: 20px;

        }

        .drag-list {
          display: flex;
          align-items: flex-start;

        }

        /* Columns */
        .drag-column {
          flex: 1;
          margin: 0 10px;
          position: relative;
          background-color: rgba(0, 0, 0, 0.4);
          border-radius: 10px;
          overflow-x: hidden;
        }

        .backlog-column .header,
        .backlog-column .solid,
        .backlog-column .solid:hover,
        .backlog-column .over {
          background-color: var(--column-1);
        }

        .progress-column .header,
        .progress-column .solid,
        .progress-column .solid:hover,
        .progress-column .over {
          background-color: var(--column-2);
        }

        .complete-column .header,
        .complete-column .solid,
        .complete-column .solid:hover,
        .complete-column .over {
          background-color: var(--column-3);
        }

        .on-hold-column .header,
        .on-hold-column .solid,
        .on-hold-column .solid:hover,
        .on-hold-column .over {
          background-color: var(--column-4);
        }

        /* Custom Scrollbar */
        .custom-scroll {
          overflow-y: auto;
          max-height: 62vh; /*75vh;*/
        }

        .custom-scroll::-webkit-scrollbar-track {
          box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.4);
          border-radius: 10px;
          background-color: rgba(255, 255, 255, 0.3);
          margin-right: 5px;
        }

        .custom-scroll::-webkit-scrollbar {
          width: 10px;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
          border-radius: 10px;
          box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
          background-color: rgba(0, 0, 0, 0.8);
        }

        .header {
          display: flex;
          justify-content: center;
          border-radius: 10px;
          margin: 10px;
        }

        .header h1 {
          font-size: 1.25rem;
        }

        /* Drag and Drop */
        .over {
          padding: 50px 10px;
        }

        .drag-item-list {
          min-height: 50px;
          
        }

        .drag-item {
          margin: 10px;
          padding: 10px;
          height: fit-content;
          background-color: rgba(0, 0, 0, 0.8);
          border-radius: 10px;
          line-height: 1.5rem;
          letter-spacing: 1px;
          cursor: pointer;
        }

        .drag-item:focus {
          outline: none;
          background-color: white;
          color: black;
        }

        /* Add Button Group */
        .add-btn-group {
          display: flex;
          justify-content: space-between;
        }

        .add-btn {
          margin: 10px;
          padding: 5px 10px;
          display: flex;
          align-items: center;
          cursor: pointer;
          width: fit-content;
          border-radius: 5px;
          transition: all 0.3s ease-in;
          user-select: none;
        }

        .add-btn:hover {
          background-color: rgba(0, 0, 0, 0.4);
          color: black;
        }

        .add-btn:active {
          transform: scale(0.90);
        }

        .solid {
          display: none;
        }

        .solid:hover {
          transition: unset;
          filter: brightness(90%);
          color: white;
          opacity: 0.5;
        }

        .plus-sign {
          font-size: 1.5rem;
          margin-right: 5px;
          position: relative;
          top: -3px;
        }

        .add-container {
          margin: 10px;
          padding: 5px 10px;
          border-radius: 10px;
          background-color: rgba(0, , 0, 0.5);
          min-height: 100px;
          display: none;
        }

        .add-item {
          width: 100%;
          min-height: 100px;
          height: auto;
          background-color: rgba(10, 10, 10, 0.5);
          border-radius: 10px;
          margin: 5px auto;
          resize: none;
          color: black;
          padding: 10px;
        }

        .add-item:focus {
          outline: none;
        }

        /* Media Query: Laptop */
        @media screen and (max-width: 1800px) {
          .main-title {
            font-size: 2rem;
          }
        }

        /* Media Query: Large Smartphone (Vertical) */
        @media screen and (max-width: 600px) {
          body {
            overflow-y: auto;
          }

          .drag-container {
            margin: 0;
          }

          .drag-list {
            display: block;
          }

          .drag-column {
            margin: 10px;
          }
        }

</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanban</title>
    <link rel="icon" type="image/png" href="../../img/logo.png">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <h1 class="main-title" style="color: black; opacity: 0.5;">PLANEJADOR EASY</h1></br>
    <div class="drag-container">
      <ul class="drag-list">
        <!-- Coluna Fazer -->
        <li  class="drag-column backlog-column">
          <span class="header">
            <h1 style="color: white;"></br>FAZER</h1>
          </span>
          <div style="opacity: 0.7;" id="backlog-content" class="custom-scroll">
            <ul style="color: white;" class="drag-item-list" id="backlog-list" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="dragEnter(0)" ondragleave="dragLeave(0)">
              <!--<li class="drag-item">Treinar</li>-->
            </ul>
          </div>
          <div class="add-btn-group">
            <div class="add-btn" onclick="showInputBox(0)">
              <span class="plus-sign">+</span>
              <span style="color: white;">ADICIONAR</span>              
            </div>
            <div class="add-btn solid" onclick="hideInputBox(0)">
              <span style="color: white;">SALVAR</span>
            </div>
          </div>
          <div class="add-container">
            <div class="add-item" contenteditable="true">              
            </div>
          </div>
        </li>
        <!-- Coluna Fazendo -->
        <li class="drag-column progress-column">
          <span class="header">
            <h1 style="color: white;"></br>FAZENDO</h1>
          </span>
          <div style="opacity: 0.7;" id="progress-content" class="custom-scroll">
            <ul style="color: white;" class="drag-item-list" id="progress-list" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="dragEnter(1)" ondragleave="dragLeave(1)">
              <!--<li class="drag-item">Assistir série</li>-->
            </ul>
          </div>
          <div class="add-btn-group">
            <div class="add-btn" onclick="showInputBox(1)">
              <span class="plus-sign">+</span>
              <span style="color: white;">ADICIONAR</span>              
            </div>
            <div class="add-btn solid" onclick="hideInputBox(1)">
              <span style="color: white;">SALVAR</span>
            </div>
          </div>
          <div class="add-container">
            <div class="add-item" contenteditable="true">              
            </div>
          </div>
        </li>
        <!-- Coluna Feito -->
        <li class="drag-column complete-column">
          <span class="header">
            <h1 style="color: white;"></br>FEITO </h1>
          </span>
          <div style="opacity: 0.7;"  id="complete-content" class="custom-scroll">
            <ul style="color: white;"class="drag-item-list" id="complete-list" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="dragEnter(2)" ondragleave="dragLeave(2)">
              <!--<li class="drag-item">Estudar</li>-->
            </ul>
          </div>
          <div class="add-btn-group">
            <div class="add-btn" onclick="showInputBox(2)">
              <span class="plus-sign">+</span>
              <span style="color: white;">ADICIONAR</span>              
            </div>
            <div class="add-btn solid" onclick="hideInputBox(2)">
              <span style="color: white;">SALVAR</span>
            </div>
          </div>
          <div class="add-container">
            <div class="add-item" contenteditable="true">              
            </div>
          </div>
        </li>
        <!-- Coluna Em Espera -->
        <li class="drag-column on-hold-column">
          <span class="header">
            <h1 style="color: white;"></br>EM ESPERA</h1>
          </span>
          <div style="opacity: 0.7;" id="on-hold-content" class="custom-scroll">
            <ul style="color: white;"  class="drag-item-list" id="on-hold-list" ondrop="drop(event)" ondragover="allowDrop(event)" ondragenter="dragEnter(3)" ondragleave="dragLeave(3)">
              <!--<li class="drag-item">Treinar</li>-->
            </ul>
          </div>
          <div class="add-btn-group">
            <div class="add-btn" onclick="showInputBox(3)">
              <span class="plus-sign">+</span>
              <span style="color: white;">ADICIONAR</span>              
            </div>
            <div class="add-btn solid" onclick="hideInputBox(3)">
              <span style="color: white;">SALVAR</span>
            </div>
          </div>
          <div class="add-container">
            <div class="add-item" contenteditable="true">
            </div>
          </div>
        </li>
      </ul>
    </div>
    <!-- Script -->
    <script src="script.js"></script>
</body>
</html>