var allParticles = [];
var maxLevel = 5;
var useFill = false;

var data = [];

var circle1;
var circle2;
var circle3;
var circle4;
var circle5;
var circle6;
var circle7;
var circle8;
var circle9;
var circle10;
var circle11;
var circle12;



// Moves to a random direction and comes to a stop.
// Spawns other particles within its lifetime.
function Particle(x, y, level) {
  this.level = level;
  this.life = 0;

  this.pos = new p5.Vector(x, y);
  this.vel = p5.Vector.random2D();
  this.vel.mult(map(this.level, 0, maxLevel, 5, 2));

  this.move = function() {
    this.life++;

    // Add friction.
    this.vel.mult(0.9);

    this.pos.add(this.vel);

    // Spawn a new particle if conditions are met.
    if (this.life % 10 == 0) {
      if (this.level > 0) {
        this.level -= 1;
        var newParticle = new Particle(this.pos.x, this.pos.y, this.level-1);
        allParticles.push(newParticle);
      }
    }
  }
}


function setup() {
  var canvas = createCanvas(windowWidth, windowHeight);
  canvas.id('sketch-container');
  pixelDensity(2.0);

  colorMode(HSB, 360);

  textAlign(CENTER);
  circle1 = new Circles(width/10.5, height/2.5, 237, 166, 111, "HAPPY");
  circle2 = new Circles(width/2.5, height/4.5, 132, 197, 119, "Relaxed");
  circle3 = new Circles(width/7.6, height/6, 230, 122, 118, "Surprised");
  circle4 = new Circles(width/3.8, height/5.8, 155, 211,	228, "Geeky");
  circle5 = new Circles(width/4.5, height/2.7, 139, 170, 170, "Bored");
  circle6 = new Circles(width/4.8, height/1.7, 110, 188, 231, "Silly");
  circle7 = new Circles(width/3.7, height/1.3, 236, 187, 154, "Paranoid");
  circle8 = new Circles(width/11, height/1.6, 156, 187, 170, "Excited");
  circle9 = new Circles(width/6.5, height/1.2, 246, 205, 113, "Contemplative");
  circle10 = new Circles(width/2.5, height/1.2, 151, 149, 240, "Melancholy");

  circle11 = new Circles(width/1.18, height/2.5, 240, 152, 25, "Angry");
  circle12 = new Circles(width/1.7, height/4.5, 119, 172, 212, "Sad");
  circle13 = new Circles(width/1.33, height/6, 253, 160, 133, "Disappointed");
  circle14 = new Circles(width/1.09, height/4.5, 161, 196, 253, "Curious");
  circle15 = new Circles(width/1.4, height/2.7, 155, 211, 228, "Lonely");
  circle16 = new Circles(width/1.11, height/1.55, 147, 213, 157, "Cheerful");
  circle17 = new Circles(width/1.2, height/1.2, 119, 172, 112, "Fearful");
  circle18 = new Circles(width/1.27, height/1.7, 206, 131, 191, "Weird");
  circle19 = new Circles(width/1.7, height/1.2, 253, 160, 133, "Loving");
  circle20 = new Circles(width/1.4, height/1.3, 161, 196, 253, "Hopeful ");
}


function draw() {
  // Create fade effect.
  noStroke();
  fill(380, 33);
  rect(0, 0, width, height);

  // Move and spawn particles.
  // Remove any that is below the velocity threshold.
  for (var i = allParticles.length-1; i > -1; i--) {
    allParticles[i].move();

    if (allParticles[i].vel.mag() < 0.01) {
      allParticles.splice(i, 1);
    }
  }

  if (allParticles.length > 0) {
    // Run script to get points to create triangles with.
    data = Delaunay.triangulate(allParticles.map(function(pt) {
      return [pt.pos.x, pt.pos.y];
    }));

    strokeWeight(0.1);

    // Display triangles individually.
    for (var i = 0; i < data.length; i += 3) {
      // Collect particles that make this triangle.
      var p1 = allParticles[data[i]];
      var p2 = allParticles[data[i+1]];
      var p3 = allParticles[data[i+2]];

      // Don't draw triangle if its area is too big.
      var distThresh = 49;

      if (dist(p1.pos.x, p1.pos.y, p2.pos.x, p2.pos.y) > distThresh) {
        continue;
      }

      if (dist(p2.pos.x, p2.pos.y, p3.pos.x, p3.pos.y) > distThresh) {
        continue;
      }

      if (dist(p1.pos.x, p1.pos.y, p3.pos.x, p3.pos.y) > distThresh) {
        continue;
      }

      // Base its hue by the particle's life.

      noFill();
      stroke(165+p1.life*1.5, 360, 360);

      triangle(p1.pos.x, p1.pos.y,
               p2.pos.x, p2.pos.y,
               p3.pos.x, p3.pos.y);
    }
  }

  // fill(0);
  // textSize(32);
  // textAlign(CENTER);
  // text("CHOOSE YOUR MOOD", width/2, height/2);
  circle1.show();
  circle1.hover();

  circle2.show();
  circle2.hover();

  circle3.show();
  circle3.hover();

  circle4.show();
  circle4.hover();

  circle5.show();
  circle5.hover();

  circle6.show();
  circle6.hover();

  circle7.show();
  circle7.hover();

  circle8.show();
  circle8.hover();

  circle9.show();
  circle9.hover();

  circle10.show();
  circle10.hover();

  circle11.show();
  circle11.hover();

  circle12.show();
  circle12.hover();

  circle13.show();
  circle13.hover();

  circle14.show();
  circle14.hover();

  circle15.show();
  circle15.hover();

  circle16.show();
  circle16.hover();

  circle17.show();
  circle17.hover();

  circle18.show();
  circle18.hover();

  circle19.show();
  circle19.hover();

  circle20.show();
  circle20.hover();

}


function mouseMoved() {
  allParticles.push(new Particle(mouseX, mouseY, maxLevel));
}






/*
Orginally from https://cdn.rawgit.com/ironwallaby/delaunay/master/delaunay.js
Tweaked it so instead of raising an error it would return an empty list.
*/

var Delaunay;

(function() {
  "use strict";

  var EPSILON = 1.0 / 1048576.0;

  function supertriangle(vertices) {
    var xmin = Number.POSITIVE_INFINITY,
        ymin = Number.POSITIVE_INFINITY,
        xmax = Number.NEGATIVE_INFINITY,
        ymax = Number.NEGATIVE_INFINITY,
        i, dx, dy, dmax, xmid, ymid;

    for(i = vertices.length; i--; ) {
      if(vertices[i][0] < xmin) xmin = vertices[i][0];
      if(vertices[i][0] > xmax) xmax = vertices[i][0];
      if(vertices[i][1] < ymin) ymin = vertices[i][1];
      if(vertices[i][1] > ymax) ymax = vertices[i][1];
    }

    dx = xmax - xmin;
    dy = ymax - ymin;
    dmax = Math.max(dx, dy);
    xmid = xmin + dx * 0.5;
    ymid = ymin + dy * 0.5;

    return [
      [xmid - 20 * dmax, ymid -      dmax],
      [xmid            , ymid + 20 * dmax],
      [xmid + 20 * dmax, ymid -      dmax]
    ];
  }

  function circumcircle(vertices, i, j, k) {
    var x1 = vertices[i][0],
        y1 = vertices[i][1],
        x2 = vertices[j][0],
        y2 = vertices[j][1],
        x3 = vertices[k][0],
        y3 = vertices[k][1],
        fabsy1y2 = Math.abs(y1 - y2),
        fabsy2y3 = Math.abs(y2 - y3),
        xc, yc, m1, m2, mx1, mx2, my1, my2, dx, dy;

    /* Check for coincident points */
    if(fabsy1y2 < EPSILON && fabsy2y3 < EPSILON)
      return;
      //throw new Error("Eek! Coincident points!");

    if(fabsy1y2 < EPSILON) {
      m2  = -((x3 - x2) / (y3 - y2));
      mx2 = (x2 + x3) / 2.0;
      my2 = (y2 + y3) / 2.0;
      xc  = (x2 + x1) / 2.0;
      yc  = m2 * (xc - mx2) + my2;
    }

    else if(fabsy2y3 < EPSILON) {
      m1  = -((x2 - x1) / (y2 - y1));
      mx1 = (x1 + x2) / 2.0;
      my1 = (y1 + y2) / 2.0;
      xc  = (x3 + x2) / 2.0;
      yc  = m1 * (xc - mx1) + my1;
    }

    else {
      m1  = -((x2 - x1) / (y2 - y1));
      m2  = -((x3 - x2) / (y3 - y2));
      mx1 = (x1 + x2) / 2.0;
      mx2 = (x2 + x3) / 2.0;
      my1 = (y1 + y2) / 2.0;
      my2 = (y2 + y3) / 2.0;
      xc  = (m1 * mx1 - m2 * mx2 + my2 - my1) / (m1 - m2);
      yc  = (fabsy1y2 > fabsy2y3) ?
        m1 * (xc - mx1) + my1 :
        m2 * (xc - mx2) + my2;
    }

    dx = x2 - xc;
    dy = y2 - yc;
    return {i: i, j: j, k: k, x: xc, y: yc, r: dx * dx + dy * dy};
  }

  function dedup(edges) {
    var i, j, a, b, m, n;

    for(j = edges.length; j; ) {
      b = edges[--j];
      a = edges[--j];

      for(i = j; i; ) {
        n = edges[--i];
        m = edges[--i];

        if((a === m && b === n) || (a === n && b === m)) {
          edges.splice(j, 2);
          edges.splice(i, 2);
          break;
        }
      }
    }
  }

  Delaunay = {
    triangulate: function(vertices, key) {
      var n = vertices.length,
          i, j, indices, st, open, closed, edges, dx, dy, a, b, c;

      /* Bail if there aren't enough vertices to form any triangles. */
      if(n < 3)
        return [];

      /* Slice out the actual vertices from the passed objects. (Duplicate the
       * array even if we don't, though, since we need to make a supertriangle
       * later on!) */
      vertices = vertices.slice(0);

      if(key)
        for(i = n; i--; )
          vertices[i] = vertices[i][key];

      /* Make an array of indices into the vertex array, sorted by the
       * vertices' x-position. */
      indices = new Array(n);

      for(i = n; i--; )
        indices[i] = i;

      indices.sort(function(i, j) {
        return vertices[j][0] - vertices[i][0];
      });

      /* Next, find the vertices of the supertriangle (which contains all other
       * triangles), and append them onto the end of a (copy of) the vertex
       * array. */
      st = supertriangle(vertices);
      vertices.push(st[0], st[1], st[2]);

      /* Initialize the open list (containing the supertriangle and nothing
       * else) and the closed list (which is empty since we havn't processed
       * any triangles yet). */
      var circCircle = circumcircle(vertices, n + 0, n + 1, n + 2);
      if (circCircle == undefined)
        return [];

      open   = [circumcircle(vertices, n + 0, n + 1, n + 2)];
      closed = [];
      edges  = [];

      /* Incrementally add each vertex to the mesh. */
      for(i = indices.length; i--; edges.length = 0) {
        c = indices[i];

        /* For each open triangle, check to see if the current point is
         * inside it's circumcircle. If it is, remove the triangle and add
         * it's edges to an edge list. */
        for(j = open.length; j--; ) {
          /* If this point is to the right of this triangle's circumcircle,
           * then this triangle should never get checked again. Remove it
           * from the open list, add it to the closed list, and skip. */
          dx = vertices[c][0] - open[j].x;
          if(dx > 0.0 && dx * dx > open[j].r) {
            closed.push(open[j]);
            open.splice(j, 1);
            continue;
          }

          /* If we're outside the circumcircle, skip this triangle. */
          dy = vertices[c][1] - open[j].y;
          if(dx * dx + dy * dy - open[j].r > EPSILON)
            continue;

          /* Remove the triangle and add it's edges to the edge list. */
          edges.push(
            open[j].i, open[j].j,
            open[j].j, open[j].k,
            open[j].k, open[j].i
          );
          open.splice(j, 1);
        }

        /* Remove any doubled edges. */
        dedup(edges);

        /* Add a new triangle for each edge. */
        for(j = edges.length; j; ) {
          b = edges[--j];
          a = edges[--j];
          open.push(circumcircle(vertices, a, b, c));
        }
      }

      /* Copy any remaining open triangles to the closed list, and then
       * remove any triangles that share a vertex with the supertriangle,
       * building a list of triplets that represent triangles. */
      for(i = open.length; i--; )
        closed.push(open[i]);
      open.length = 0;

      for(i = closed.length; i--; )
        if(closed[i].i < n && closed[i].j < n && closed[i].k < n)
          open.push(closed[i].i, closed[i].j, closed[i].k);

      /* Yay, we're done! */
      return open;
    },
    contains: function(tri, p) {
      /* Bounding box test first, for quick rejections. */
      if((p[0] < tri[0][0] && p[0] < tri[1][0] && p[0] < tri[2][0]) ||
         (p[0] > tri[0][0] && p[0] > tri[1][0] && p[0] > tri[2][0]) ||
         (p[1] < tri[0][1] && p[1] < tri[1][1] && p[1] < tri[2][1]) ||
         (p[1] > tri[0][1] && p[1] > tri[1][1] && p[1] > tri[2][1]))
        return null;

      var a = tri[1][0] - tri[0][0],
          b = tri[2][0] - tri[0][0],
          c = tri[1][1] - tri[0][1],
          d = tri[2][1] - tri[0][1],
          i = a * d - b * c;

      /* Degenerate tri. */
      if(i === 0.0)
        return null;

      var u = (d * (p[0] - tri[0][0]) - b * (p[1] - tri[0][1])) / i,
          v = (a * (p[1] - tri[0][1]) - c * (p[0] - tri[0][0])) / i;

      /* If we're outside the tri, fail. */
      if(u < 0.0 || v < 0.0 || (u + v) > 1.0)
        return null;

      return [u, v];
    }
  };

  if(typeof module !== "undefined")
    module.exports = Delaunay;
})();



// mood circles

class Circles {
  constructor(x, y, colr, colg, colb, mood){
    this.x = x;
    this.y = y;
    this.colr = colr;
    this.colg = colg;
    this.colb = colb;
    this.mood = mood;
    this.r = 117;
  }

  show() {
    colorMode(RGB);
    noStroke();
    // stroke(this.colr-20, this.colg-20, this.colb-20);
    // strokeWeight(3);
    fill(this.colr, this.colg, this.colb);
    ellipseMode(CENTER);
    ellipse(this.x, this.y, this.r);
    fill(255);
    textSize(20);
    if (this.mood == "Contemplative" || this.mood == "Disappointed") {
      textSize(17);
    }
    textAlign(CENTER);
    text(this.mood, this.x, this.y+5);

    stroke(this.colr, this.colg, this.colb, 80);
    strokeWeight(0.8);
    noFill();
    ellipse(this.x, this.y, this.r+7.5);
    noStroke();

    colorMode(HSB);
  }

  hover() {
    colorMode(RGB);
    // get distance between mouse and circle
    var distance = dist(mouseX, mouseY, this.x, this.y);
    if(distance < this.r) {
      fill(this.colr+30, this.colg+20, this.colb+20);
      ellipse(this.x, this.y, this.r+10);
      fill(255);
      textSize(22);
      if (this.mood == "Contemplative" || this.mood == "Disappointed") {
        textSize(19);
      }
      textAlign(CENTER);
      text(this.mood, this.x, this.y+5);
    }
    colorMode(HSB);
  }
}
